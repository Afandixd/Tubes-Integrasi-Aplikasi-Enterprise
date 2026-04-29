<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use App\Models\Payment;

Route::post('/payments', function (Request $request) {
    $orderId = $request->order_id;

    if (!$orderId) {
        return response()->json(["message" => "Order ID is required"], 400);
    }

    $orderResponse = Http::get("http://localhost:8003/api/orders/$orderId");
    $order = $orderResponse->json();

    if ($orderResponse->failed() || isset($order['message']) || !$order) {
        return response()->json(["message" => "Order tidak ditemukan"], 404);
    }

    $status = "LUNAS";
    if (($order['user']['name'] ?? '') == 'Data tidak ditemukan' || ($order['product']['name'] ?? '') == 'Data tidak ditemukan') {
        $status = "Tidak diketahui";
    }

    $payment = Payment::create([
        "order_id" => $orderId,
        "status" => $status
    ]);

    return [
        "payment_id" => $payment->id,
        "order_id" => $order['order_id'],
        "user_name" => $order['user']['name'] ?? 'Data tidak ditemukan',
        "product_name" => $order['product']['name'] ?? 'Data tidak ditemukan',
        "status" => $payment->status,
        "paid_at" => $payment->created_at
    ];
});

Route::get('/payments', function () {
    return Payment::all();
});