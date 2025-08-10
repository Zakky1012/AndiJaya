<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePassengerDetailRequest;
use App\interfaces\KeberangkatanRepositoryInterface;
use App\interfaces\TransactionRepositoryInterface;
use Illuminate\Http\Request;
use App\Models\ClassKeberangkatan;
use App\Models\PromoCode;
use Illuminate\Support\Facades\Session;

class BookingController extends Controller
{
    private KeberangkatanRepositoryInterface $keberangkatanRepository;
    private TransactionRepositoryInterface $transactionRepository;

    public function __construct(
        KeberangkatanRepositoryInterface $keberangkatanRepository,
        TransactionRepositoryInterface $transactionRepository
    ) {
        $this->keberangkatanRepository = $keberangkatanRepository;
        $this->transactionRepository = $transactionRepository;
    }

    public function booking(Request $request, $nomorKeberangkatan)
    {
        $this->transactionRepository->saveTransactionDataToSession($request->all());
        return redirect()->route('booking.chooseSeat', ['nomorKeberangkatan' => $nomorKeberangkatan]);
    }

    public function chooseSeat(Request $request, $nomorKeberangkatan)
    {
        $transaction = $this->transactionRepository->getTransactionDataFromSession();
        $keberangkatan = $this->keberangkatanRepository->getAllKeberangkatanByNomorKeberangkatan($nomorKeberangkatan);
        $tier = $keberangkatan->classes->find($transaction['keberangkatan_class_id']);
        return view('pages.booking.choose-seat', compact('transaction', 'keberangkatan', 'tier'));
    }

    public function confirmSeat(Request $request, $nomorKeberangkatan)
    {
        $this->transactionRepository->saveTransactionDataToSession($request->all());
        return redirect()->route('booking.passengerDetails', ['nomorKeberangkatan' => $nomorKeberangkatan]);
    }

    public function passengerDetails(Request $request, $nomorKeberangkatan)
    {
        $transaction = $this->transactionRepository->getTransactionDataFromSession();
        $keberangkatan = $this->keberangkatanRepository->getAllKeberangkatanByNomorKeberangkatan($nomorKeberangkatan);
        $tier = $keberangkatan->classes->find($transaction['keberangkatan_class_id']);
        return view('pages.booking.passenger-details', compact('transaction', 'keberangkatan', 'tier'));
    }

    public function savePassengerDetails(Request $request, $nomorKeberangkatan)
    {
        $this->transactionRepository->saveTransactionDataToSession($request->all());
        return redirect()->route('booking.checkout', ['nomorKeberangkatan' => $nomorKeberangkatan]);
    }

    public function checkout($nomorKeberangkatan)
    {
        $transaction = $this->transactionRepository->getTransactionDataFromSession();
        $keberangkatan = $this->keberangkatanRepository->getAllKeberangkatanByNomorKeberangkatan($nomorKeberangkatan);
        $tier = $keberangkatan->classes->find($transaction['keberangkatan_class_id']);

        // --- Bagian Perhitungan Diskon dan Total Baru ---
        $hargaPerSeat = $tier->harga;
        $numberOfPassengers = count($transaction['selected_seats']);
        $subTotal = $hargaPerSeat * $numberOfPassengers;

        $diskon = 0;
        if (isset($transaction['promo_code']) && !empty($transaction['promo_code'])) {
            $promo = PromoCode::where('kode', $transaction['promo_code'])
                ->where('valid', '>=', now())
                ->where('is_used', false)
                ->first();

            if ($promo) {
                if ($promo->tipe_diskon === 'percentage') {
                    $diskon = $subTotal * ($promo->diskon / 100);
                } else {
                    $diskon = $promo->diskon;
                }
            }
        }
        $totalSetelahDiskon = $subTotal - $diskon;
        if ($totalSetelahDiskon < 0) {
            $totalSetelahDiskon = 0;
        }

        $ppn = $totalSetelahDiskon * 0.11;
        $grandTotal = $totalSetelahDiskon + $ppn;
        // --- Akhir Bagian Perhitungan ---

        return view('pages.booking.checkout', compact('transaction', 'keberangkatan', 'tier', 'subTotal', 'diskon', 'ppn', 'grandTotal'));
    }

    public function payment(Request $request, $nomorKeberangkatan) {
        $existingData = $this->transactionRepository->getTransactionDataFromSession();
        $mergedData = array_merge($existingData, $request->all());

        $transaction = $this->transactionRepository->saveTransaction($mergedData);

        \Midtrans\Config::$serverKey = config('midtrans.serverKey');
        \Midtrans\Config::$isProduction = config('midtrans.isProduction');
        \Midtrans\Config::$isSanitized = config('midtrans.isSanitized');
        \Midtrans\Config::$is3ds = config('midtrans.is3ds');

        $grossAmount = intval(round($transaction->grand_total));
        if ($grossAmount <= 0) {
            return redirect()->back()->with('error', 'Grand total tidak valid. Transaksi dibatalkan.');
        }

        $params = [
            'transaction_details' => [
                'order_id' => $transaction->kode,
                'gross_amount' => $grossAmount,
            ],
            'customer_details' => [
                'first_name' => $transaction->nama,
                'email' => $transaction->email,
                'phone' => $transaction->nomor,
            ],
        ];

        try {
            $paymentUrl = \Midtrans\Snap::createTransaction($params)->redirect_url;
            return redirect($paymentUrl);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
    }

    public function success(Request $request)
    {
        $transaction = $this->transactionRepository->getTransactionByCode($request->order_id);
        if (!$transaction) {
            return redirect()->route('home');
        }
        return view('pages.booking.success', compact('transaction'));
    }

    public function checkBooking()
    {
        return view('pages.booking.check-booking');
    }
}
