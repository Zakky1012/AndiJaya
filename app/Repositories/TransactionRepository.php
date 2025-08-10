<?php

namespace App\Repositories;

use App\interfaces\TransactionRepositoryInterface;
use App\Models\ClassKeberangkatan;
use App\Models\PromoCode;
use App\Models\Transaksi;
use App\Models\TransaksiPassenger;
use Illuminate\Support\Facades\Session;

class TransactionRepository implements TransactionRepositoryInterface
{
    public function getTransactionDataFromSession()
    {
        return Session::get('transaksi');
    }

    public function saveTransactionDataToSession($data)
    {
        $transaction = Session::get('transaksi', []);

        $mergedData = array_merge($transaction, $data);

        Session::put('transaksi', $mergedData);
    }

    public function saveTransaction($data)
    {
        if (!isset($data['passengers']) || !is_array($data['passengers'])) {
            throw new \Exception("Data penumpang tidak ditemukan dalam sesi. Pastikan Anda mengisi form penumpang terlebih dahulu.");
        }

        $data['kode'] = $this->generateTransactionCode();
        $data['nomor_passenger'] = $this->countPassengers($data['passengers']);

        // 1. Hitung Subtotal
        $hargaPerSeat = ClassKeberangkatan::findOrFail($data['keberangkatan_class_id'])->harga;
        $subTotal = $hargaPerSeat * $data['nomor_passenger'];
        $data['sub_total'] = $subTotal;

        $diskon = 0;
        $promoId = null;

        // 2. Terapkan diskon jika ada
        if (isset($data['promo_code']) && !empty($data['promo_code'])) {
            $promo = PromoCode::where('kode', $data['promo_code'])
                ->where('valid', '>=', now())
                ->where('is_used', false)
                ->first();

            if ($promo) {
                if ($promo->tipe_diskon === 'percentage') {
                    $diskon = $subTotal * ($promo->diskon / 100);
                } else {
                    $diskon = $promo->diskon;
                }
                $promoId = $promo->id;
            }
        }
        $data['diskon'] = $diskon;
        $data['kode_promo_id'] = $promoId;

        // 3. Hitung PPN dari harga Subtotal awal
        $ppn = $subTotal * 0.11;
        $data['total_tax'] = $ppn;

        // 4. Hitung Grand Total
        $data['grand_total'] = ($subTotal - $diskon) + $ppn;
        if ($data['grand_total'] < 0) {
            $data['grand_total'] = 0;
        }

        // Simpan transaksi dan penumpang
        $transaksi = $this->createTransaction($data);
        $this->savePassengers($data['passengers'], $transaksi->id);

        Session::forget('transaksi');

        return $transaksi;
    }

    private function generateTransactionCode()
    {
        return "ANDIJAYA" . rand(1000, 9999);
    }

    private function countPassengers($passengers)
    {
        return count($passengers);
    }

    private function createTransaction($data)
    {
        return Transaksi::create($data);
    }

    private function savePassengers($passengers, $transactionId)
    {
        foreach ($passengers as $passenger) {
            $passenger['transaksi_id'] = $transactionId;
            TransaksiPassenger::create($passenger);
        }
    }

    public function getTransactionByCode($kode)
    {
        return Transaksi::where('kode', $kode)->first();
    }

    public function getTransactionByCodePhone($kode, $nomor)
    {
        return Transaksi::where('kode', $kode)->where('nomor', $nomor)->first();
    }
}
