<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

Route::get('/products', function () {
    return Cache::get('products', [
        ["id" => 1, "name" => "Kopi", "price" => 15000],
        ["id" => 2, "name" => "Roti", "price" => 20000]
    ]);
});

Route::post('/products', function (Request $request) {
    $products = Cache::get('products', [
        ["id" => 1, "name" => "Kopi", "price" => 15000],
        ["id" => 2, "name" => "Roti", "price" => 20000]
    ]);

    $newProduct = [
        "id" => count($products) > 0 ? max(array_column($products, 'id')) + 1 : 1,
        "name" => $request->name,
        "price" => $request->price
    ];

    $products[] = $newProduct;
    Cache::forever('products', $products);

    return [
        "message" => "Product created",
        "data" => $newProduct
    ];
});

Route::get('/products/{id}', function ($id) {
    $products = Cache::get('products', [
        ["id" => 1, "name" => "Kopi", "price" => 15000],
        ["id" => 2, "name" => "Roti", "price" => 20000]
    ]);

    foreach ($products as $product) {
        if ($product['id'] == $id) return $product;
    }

    return response()->json(["message" => "Product not found"], 404);
});

// METHOD PUT: Update Produk
Route::put('/products/{id}', function (Request $request, $id) {
    $products = Cache::get('products', [
        ["id" => 1, "name" => "Kopi", "price" => 15000],
        ["id" => 2, "name" => "Roti", "price" => 20000]
    ]);

    foreach ($products as &$product) {
        if ($product['id'] == $id) {
            $product['name'] = $request->name ?? $product['name'];
            $product['price'] = $request->price ?? $product['price'];
            Cache::forever('products', $products);
            return ["message" => "Product updated", "data" => $product];
        }
    }

    return response()->json(["message" => "Product not found"], 404);
});

// METHOD DELETE: Hapus Produk
Route::delete('/products/{id}', function ($id) {
    $products = Cache::get('products', [
        ["id" => 1, "name" => "Kopi", "price" => 15000],
        ["id" => 2, "name" => "Roti", "price" => 20000]
    ]);

    $filteredProducts = array_filter($products, function($p) use ($id) {
        return $p['id'] != $id;
    });

    if (count($products) === count($filteredProducts)) {
        return response()->json(["message" => "Product not found"], 404);
    }

    Cache::forever('products', array_values($filteredProducts));

    return ["message" => "Product deleted"];
});