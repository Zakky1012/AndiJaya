<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransController extends Controller
{
    /**
     * Menerima notifikasi dari Midtrans.
     * Endpoint ini HARUS bisa diakses publik (contoh: domain.com/api/midtrans/callback).
     */
    public function callback(Request $request)
    {
        // Mendapatkan server key dari konfigurasi.
        $serverKey = config('midtrans.serverKey');

        // Membentuk string untuk hashing dan validasi signature key
        $stringToHash = $request->order_id . $request->status_code . $request->gross_amount . $serverKey;
        $calculatedHashedKey = hash('sha512', $stringToHash);

        // --- Perbaikan pada logika validasi signature key ---
        // Perbandingan yang benar adalah langsung membandingkan kedua string.
        // Logika `$calculatedHashedKey !== $request->signature_key` adalah satu-satunya yang diperlukan.
        // Kode sebelumnya `if (!$hashedKey !== $request->signature_key)` selalu bernilai true.
        if ($calculatedHashedKey !== $request->signature_key) {
            Log::error('Midtrans callback: Invalid signature key.', [
                'request_signature_key' => $request->signature_key,
                'calculated_signature_key' => $calculatedHashedKey,
                'order_id' => $request->order_id
            ]);
            return response()->json(['message' => 'invalid signature key'], 403);
        }

        // Mencari data transaksi berdasarkan order ID
        $orderId = $request->order_id;
        $transaction = Transaksi::where('kode', $orderId)->first();

        // Jika transaksi tidak ditemukan
        if (!$transaction) {
            Log::warning('Midtrans callback: Transaction not found.', ['order_id' => $orderId]);
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        // Memproses status transaksi dari Midtrans
        $transactionStatus = $request->transaction_status;

        switch ($transactionStatus) {
            case 'capture':
                if ($request->payment_type == 'credit_card') {
                    if ($request->fraud_status == 'challenge') {
                        $transaction->update(['status_payment' => 'pending']);
                    } else { // fraud_status == 'accept'
                        $transaction->update(['status_payment' => 'dibayar']);
                        foreach ($transaction->passengers as $passenger) {
                            $passenger->seat->update(['is_available' => false]);
                        }
                    }
                } else {
                    $transaction->update(['status_payment' => 'dibayar']);
                    foreach ($transaction->passengers as $passenger) {
                        $passenger->seat->update(['is_available' => false]);
                    }
                }
                break;
            case 'settlement':
                $transaction->update(['status_payment' => 'dibayar']);
                foreach ($transaction->passengers as $passenger) {
                    $passenger->seat->update(['is_available' => false]);
                }
                break;
            case 'pending':
                $transaction->update(['status_payment' => 'pending']);
                break;
            case 'deny':
                $transaction->update(['status_payment' => 'gagal']);
                break; // --- Perbaikan: Tambahan 'break' di sini ---
            case 'expire':
            case 'cancel':
                $transaction->update(['status_payment' => 'gagal']);
                break;
            default:
                $transaction->update(['status_payment' => 'gagal']);
                break;
        }

        return response()->json(['message' => 'Callback processed successfully'], 200);
    }
}
