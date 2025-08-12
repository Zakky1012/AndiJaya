<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KeberangkatanController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('keberangkatans', [KeberangkatanController::class, 'index'])->name('keberangkatan.index');
Route::get('keberangkatan/{nomorKeberangkatan}/choose-tier', [KeberangkatanController::class, 'show'])->name('keberangkatan.show');

Route::get('keberangkatan/booking/{nomorKeberangkatan}', [BookingController::class, 'booking'])->name('booking');

Route::get('keberangkatan/booking/{nomorKeberangkatan}/choose-seat', [BookingController::class, 'chooseSeat'])->name('booking.chooseSeat');
Route::post('keberangkatan/booking/{nomorKeberangkatan}/confirm-seat', [BookingController::class, 'confirmSeat'])->name('booking.confirmSeat');

Route::get('keberangkatan/booking/{nomorKeberangkatan}/passenger-details', [BookingController::class, 'passengerDetails'])->name('booking.passengerDetails');
Route::post('keberangkatan/booking/{nomorKeberangkatan}/save-passenger-details', [BookingController::class, 'savePassengerDetails'])->name('booking.savePassengerDetails');

Route::get('/keberangkatan/booking/{nomorKeberangkatan}/checkout', [BookingController::class, 'checkout'])->name('booking.checkout');
Route::post('/keberangkatan/booking/{nomorKeberangkatan}/payment', [BookingController::class, 'payment'])->name('booking.payment');

Route::get('/booking-success', [BookingController::class, 'success'])->name('booking.success');

Route::get('check-booking', [BookingController::class, 'checkBooking'])->name('booking.check');
Route::post('check-booking', [BookingController::class, 'show'])->name('booking.show');
