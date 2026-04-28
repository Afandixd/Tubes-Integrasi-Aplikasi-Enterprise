<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;

Route::post('/orders', function (Request $request) {
    $userId = $request->user_id;
    $productId = $request->product_id;

    if (!$userId || !$productId) {
        return response()->json(["message" => "User ID and Product ID are required"], 400);
    }

    // Komunikasi Antar Service
    $userResponse = Http::get("http://localhost:8001/api/users/$userId");
    $productResponse = Http::get("http://localhost:8002/api/products/$productId");

    $user = $userResponse->json();
    $product = $productResponse->json();

    // Validasi apakah service lain mengembalikan data yang benar
    if ($userResponse->failed() || $productResponse->failed() || isset($user['message']) || isset($product['message'])) {
        return response()->json(["message" => "User atau Product tidak ditemukan"], 404);
    }

    // Pastikan data bukan array koleksi (jika service salah mengembalikan list)
    if (isset($user[0])) $user = $user[0];
    if (isset($product[0])) $product = $product[0];

    $orders = Cache::get('orders', []);
    
    $newOrder = [
        "order_id" => count($orders) + 1,
        "user" => $user,
        "product" => $product,
        "status" => "pending",
        "created_at" => now()
    ];

    $orders[] = $newOrder;
    Cache::forever('orders', $orders);

    return $newOrder;
});

Route::get('/orders', function () {
    return Cache::get('orders', []);
});

Route::get('/orders/{id}', function ($id) {
    $orders = Cache::get('orders', []);
    foreach ($orders as $order) {
        if ($order['order_id'] == $id) return $order;
    }
    return response()->json(["message" => "Order tidak ditemukan"], 404);
});