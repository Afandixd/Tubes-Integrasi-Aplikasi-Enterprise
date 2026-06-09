<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use App\Models\Order;
use App\Jobs\UpdateProductStock;
use App\Jobs\ProcessPayment;

function fetchOrderDetails($order) {
    $userServiceUrl = env('USER_SERVICE_URL', 'http://localhost:8000');
    $productServiceUrl = env('PRODUCT_SERVICE_URL', 'http://localhost:8001');

    $userResponse = Http::get("{$userServiceUrl}/api/users/{$order->user_id}");
    $productResponse = Http::get("{$productServiceUrl}/api/products/{$order->product_id}");

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
    try {
        $userId = $request->user_id;
        $productId = $request->product_id;
        $quantity = $request->quantity ?? 1;

        if (!$userId || !$productId) {
            return response()->json(["message" => "User ID dan Product ID harus diisi"], 400);
        }

        // TEMPORARY: Product validation disabled for demo
        // Will be enabled when Salwa's Product Service is ready
        $productData = ['price' => 100000]; // Mock data for demo

        // Simpan Order
        $order = Order::create([
            "user_id" => $userId,
            "product_id" => $productId,
            "status" => "pending"
        ]);

        $totalPrice = $productData['price'] * $quantity;

        // Kirim ke RabbitMQ
        UpdateProductStock::dispatch($productId, $quantity)->onQueue('product_queue');
        ProcessPayment::dispatch($order->id, $totalPrice)->onQueue('payment_queue');

        return fetchOrderDetails($order);

    } catch (\Throwable $e) {
        // Ini akan menampilkan pesan error aslinya di Postman
        return response()->json([
            "error_asli" => $e->getMessage(),
            "file" => $e->getFile(),
            "line" => $e->getLine()
        ], 500);
    }
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
