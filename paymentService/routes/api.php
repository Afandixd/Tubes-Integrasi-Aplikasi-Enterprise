<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Payment;
use App\Jobs\ProcessPayment;

// CREATE PAYMENT
Route::post('/payments', function (Request $request) {

    $payment = Payment::create([
        'total_pesanan' => $request->total_pesanan,
        'status' => $request->status ?? 'pending'
    ]);

    // Kirim data ke RabbitMQ
    ProcessPayment::dispatch(
        $payment->id,
        $payment->total_pesanan,
        $payment->status
    )->onQueue('payment_queue');

    return response()->json([
        'message' => 'Payment berhasil dibuat',
        'data' => [
            'id' => $payment->id,
            'total_pesanan' => $payment->total_pesanan,
            'status' => $payment->status
        ]
    ]);
});

// GET ALL PAYMENTS
Route::get('/payments', function () {

    return Payment::select(
        'id',
        'total_pesanan',
        'status'
    )->get();

});

// GET PAYMENT BY ID
Route::get('/payments/{id}', function ($id) {

    $payment = Payment::select(
        'id',
        'total_pesanan',
        'status'
    )->find($id);

    if (!$payment) {
        return response()->json([
            'message' => 'Payment tidak ditemukan'
        ], 404);
    }

    return $payment;
});

// UPDATE PAYMENT
Route::put('/payments/{id}', function (Request $request, $id) {

    $payment = Payment::find($id);

    if (!$payment) {
        return response()->json([
            'message' => 'Payment tidak ditemukan'
        ], 404);
    }

    $payment->update([
        'total_pesanan' => $request->total_pesanan ?? $payment->total_pesanan,
        'status' => $request->status ?? $payment->status
    ]);

    return response()->json([
        'message' => 'Payment berhasil diperbarui',
        'data' => [
            'id' => $payment->id,
            'total_pesanan' => $payment->total_pesanan,
            'status' => $payment->status
        ]
    ]);
});

// DELETE PAYMENT
Route::delete('/payments/{id}', function ($id) {

    $payment = Payment::find($id);

    if (!$payment) {
        return response()->json([
            'message' => 'Payment tidak ditemukan'
        ], 404);
    }

    $payment->delete();

    return response()->json([
        'message' => 'Payment berhasil dihapus'
    ]);
});

// TEST RABBITMQ
Route::get('/test-queue', function () {

    ProcessPayment::dispatch(
        999,
        50000,
        'pending'
    )->onQueue('payment_queue');

    return response()->json([
        'message' => 'Job berhasil dikirim ke RabbitMQ'
    ]);
});
