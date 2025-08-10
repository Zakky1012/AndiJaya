<?php

namespace App\Repositories;

use App\interfaces\TransactionRepositoryInterface;
use App\Models\ClassKeberangkatan;
use App\Models\PromoCode;
use App\Models\Transaksi;
use App\Models\TransaksiPassenger;

class TransactionRepository implements TransactionRepositoryInterface
{

    public function getTransactionDataFromSession()
    {
        return session()->get('transaksi');
    }

    public function saveTransactionDataToSession($data)
    {
        $transaction = session()->get('transaksi', []);

        foreach ($data as $key => $value) {
            $transaction[$key] = $value;
        }

        session()->put('transaksi', $transaction);
    }

    public function saveTransaction($data)
    {
        if (!isset($data['passengers']) || !is_array($data['passengers'])) {
            throw new \Exception("Data penumpang tidak ditemukan dalam sesi. Pastikan Anda mengisi form penumpang terlebih dahulu.");
        }

        $data['kode'] = $this->generateTransactionCode();
        $data['nomor_passenger'] = $this->countPassengers($data['passengers']);

        // hitung subtotal dan grand total awal
        $data['sub_total'] = $this->calculateSubTotal($data['keberangkatan_class_id'], $data['nomor_passenger']);
        $data['grand_total'] = $data['sub_total'];

        // terapkan promo jika ada
        if(!empty($data['kode_promo_id'])) {
        $data = $this->applyPromoCode($data);
        }

        // Menambah PPN
        $data['grand_total'] = $this->addPPN($data['grand_total']);

        // simpan transaksi dan penumpang
        $transaksi = $this->createTransaction($data);
        $this->savePassengers($data['passengers'], $transaksi->id);

        session()->forget('transaksi');

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

    private function calculateSubTotal($keberangkatanClassId, $numberOfPassengers)
    {
        $price  = ClassKeberangkatan::findOrFail($keberangkatanClassId)->harga;
        return $price * $numberOfPassengers;
    }

    private function applyPromoCode($data) {
        $promo = PromoCode::where('kode', $data['promo_code'])
        ->where('valid', '>=', now())
        ->where('is_used', false)
        ->first();

        if ($promo) {
        if ($promo->tipe_diskon === 'percentage') {
            $data['diskon']     = $data['grand_total'] * ($promo->diskon / 100);
        } else {
            $data['diskon']     = $promo->diskon;
        }

        $data['grand_total']    -= $data['diskon'];
        $data['kode_promo_id']  = $promo->id ;

        // tandai promo code sebagai sudah digunakan
        $promo->update(['is_used' => true]);
        }

        return $data;
    }

    private function addPpn($grandTotal)
    {
        $ppn = $grandTotal * 0.11;
        return $grandTotal + $ppn;
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

