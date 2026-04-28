<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

Route::get('/users', function () {
    // Gunakan Cache agar data tidak hilang di API
    return Cache::get('users', [
        ["id" => 1, "name" => "Afandi"],
        ["id" => 2, "name" => "Salwa"]
    ]);
});

Route::get('/users/{id}', function ($id) {
    $users = Cache::get('users', [
        ["id" => 1, "name" => "Afandi"],
        ["id" => 2, "name" => "Salwa"]
    ]);

    foreach ($users as $user) {
        if ($user['id'] == $id) return $user;
    }

    return response()->json(["message" => "user tidak ditemukan"], 404);
});

Route::post('/users', function (Request $request) {
    $users = Cache::get('users', [
        ["id" => 1, "name" => "Afandi"],
        ["id" => 2, "name" => "Salwa"]
    ]);

    $newUser = [
        "id" => count($users) > 0 ? max(array_column($users, 'id')) + 1 : 1,
        "name" => $request->name
    ];

    $users[] = $newUser;
    Cache::forever('users', $users);

    return ["message" => "user berhasil dibuat.", "data" => $newUser];
});

// METHOD PUT: Update User
Route::put('/users/{id}', function (Request $request, $id) {
    $users = Cache::get('users', [
        ["id" => 1, "name" => "Afandi"],
        ["id" => 2, "name" => "Salwa"]
    ]);

    foreach ($users as &$user) {
        if ($user['id'] == $id) {
            $user['name'] = $request->name;
            Cache::forever('users', $users);
            return ["message" => "user berhasil diupdate", "data" => $user];
        }
    }

    return response()->json(["message" => "user tidak ditemukan"], 404);
});

// METHOD DELETE: Hapus User
Route::delete('/users/{id}', function ($id) {
    $users = Cache::get('users', [
        ["id" => 1, "name" => "Afandi"],
        ["id" => 2, "name" => "Salwa"]
    ]);

    $filteredUsers = array_filter($users, function($user) use ($id) {
        return $user['id'] != $id;
    });

    if (count($users) === count($filteredUsers)) {
        return response()->json(["message" => "user tidak ditemukan"], 404);
    }

    Cache::forever('users', array_values($filteredUsers));

    return ["message" => "user berhasil dihapus"];
});