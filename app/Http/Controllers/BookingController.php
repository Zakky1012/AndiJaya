<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePassengerDetailRequest;
use App\interfaces\KeberangkatanRepositoryInterface;
use App\interfaces\TransactionRepositoryInterface;
use Illuminate\Http\Request;

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

        return view('pages.booking.checkout', compact('transaction', 'keberangkatan', 'tier'));
    }

    public function payment(Request $request) {
        $this->transactionRepository->getTransactionDataFromSession($request->all());

        $transaction = $this->transactionRepository->saveTransaction (
            $this->transactionRepository->getTransactionDataFromSession()
        );

        \Midtrans\Config::$serverKey     = config('midtrans.serverKey');
        \Midtrans\Config::$isProduction  = config('midtrans.isProduction');
        \Midtrans\Config::$isSanitized   = config('midtrans.isSanitized');
        \Midtrans\Config::$is3ds         = config('midtrans.is3ds');

        $params = [
            'transaction_details'   => [
                'order_id'          => $transaction->kode,
                'gross_amount'      => intval(round($transaction->grand_total)),
            ]
        ];

        $paymentUrl = \Midtrans\Snap::createTransaction($params)->redirect_url;

        return redirect($paymentUrl);

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
