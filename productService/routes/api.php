<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Product;

Route::get('/products', function () {
    return Product::all();
});

Route::post('/products', function (Request $request) {
    $product = Product::create([
        "name" => $request->name,
        "price" => $request->price,
        "stock" => $request->stock ?? 10
    ]);
    return ["message" => "Product created", "data" => $product];
});

Route::get('/products/{id}', function ($id) {
    return Product::find($id) ?: response()->json(["message" => "Product not found"], 404);
});

Route::put('/products/{id}', function (Request $request, $id) {
    $product = Product::find($id);
    if (!$product) return response()->json(["message" => "Product not found"], 404);
    
    $product->update($request->only(['name', 'price', 'stock']));
    return ["message" => "Product updated", "data" => $product];
});

// Jalur pintas untuk update stok via POST (lebih stabil)
Route::post('/products/{id}/update-stock', function (Request $request, $id) {
    $product = Product::find($id);
    if (!$product) return response()->json(["message" => "Product not found"], 404);
    
    $product->update(['stock' => $request->stock]);
    return ["message" => "Stock updated via POST", "data" => $product];
});

Route::delete('/products/', function () {
    Product::truncate();
    return ["message" => "Semua produk berhasil dihapus"];
});

Route::delete('/products/{id}', function ($id) {
    $product = Product::find($id);
    if (!$product) return response()->json(["message" => "Product not found"], 404);
    
    $product->delete();
    return ["message" => "Product deleted"];
});