<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use App\Models\Order;

function fetchOrderDetails($order) {
    $userResponse = Http::get("http://localhost:8001/api/users/{$order->user_id}");
    $productResponse = Http::get("http://localhost:8002/api/products/{$order->product_id}");

    $user = $userResponse->json();
    $product = $productResponse->json();

    $status = $order->status;
    
    // Check if data is actually found or missing
    if ($userResponse->failed() || (isset($user['message']) && $user['message'] == "user tidak ditemukan")) {
        $user = ['name' => 'Data tidak ditemukan'];
        $status = "Tidak diketahui";
    }
    
    if ($productResponse->failed() || (isset($product['message']) && $product['message'] == "Product not found")) {
        $product = ['name' => 'Data tidak ditemukan'];
        $status = "Tidak diketahui";
    }

    return [
        "order_id" => $order->id,
        "user" => $user,
        "product" => $product,
        "status" => $status,
        "created_at" => $order->created_at
    ];
}

Route::post('/orders', function (Request $request) {
    $userId = $request->user_id;
    $productId = $request->product_id;

    if (!$userId || !$productId) {
        return response()->json(["message" => "User ID and Product ID are required"], 400);
    }

    $order = Order::create([
        "user_id" => $userId,
        "product_id" => $productId,
        "status" => "pending"
    ]);

    return fetchOrderDetails($order);
});

Route::get('/orders', function () {
    $orders = Order::all();
    return $orders->map(function($order) {
        return fetchOrderDetails($order);
    });
});

Route::get('/orders/{id}', function ($id) {
    $order = Order::find($id);
    if (!$order) return response()->json(["message" => "Order tidak ditemukan"], 404);
    return fetchOrderDetails($order);
});