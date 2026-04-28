<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;

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

    // Safely get user and product names, handling array wrapper if present
    $user = $order['user'];
    $product = $order['product'];
    
    $userName = isset($user[0]) ? ($user[0]['name'] ?? 'Unknown') : ($user['name'] ?? 'Unknown');
    $productName = isset($product[0]) ? ($product[0]['name'] ?? 'Unknown') : ($product['name'] ?? 'Unknown');

    $result = [
        "order_id" => $order['order_id'],
        "user_name" => $userName,
        "product_name" => $productName,
        "status" => "LUNAS",
        "paid_at" => now()
    ];

    $payments = Cache::get('payments', []);
    $payments[] = $result;
    Cache::forever('payments', $payments);

    return $result;
});

Route::get('/payments', function () {
    return Cache::get('payments', []);
});