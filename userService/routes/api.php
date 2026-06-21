<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

Route::get('/users', function () {
    return User::all();
});

Route::get('/users/{id}', function ($id) {
    return User::find($id) ?: response()->json(["message" => "user tidak ditemukan"], 404);
});

Route::post('/users', function (Request $request) {
    $user = User::create([
        "name" => $request->name
    ]);
    return ["message" => "user berhasil dibuat.", "data" => $user];
});

Route::put('/users/{id}', function (Request $request, $id) {
    $user = User::find($id);
    if (!$user) return response()->json(["message" => "user tidak ditemukan"], 404);
    
    $user->update(["name" => $request->name]);
    return ["message" => "user berhasil diupdate", "data" => $user];
});

Route::delete('/users/{id}', function ($id) {
    $user = User::find($id);
    if (!$user) return response()->json(["message" => "user tidak ditemukan"], 404);
    
    $user->delete();
    return ["message" => "user berhasil dihapus"];
});