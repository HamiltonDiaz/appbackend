<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

// Route::prefix("v1/users")->group(function () {
//     Route::post('register', [UserController::class, 'userRegister']);
//     Route::post('login', [UserController::class, 'login']);    
// });

// // Rutas protegidas por el middleware JWT
// Route::prefix("v1/users")->middleware('auth')->group(function () {
//     Route::post('logout', [UserController::class, 'logout']);
//     Route::get('me', [UserController::class, 'me']);
// });
Route::prefix("v1/users")->group(function () {
    Route::post('login', [UserController::class, 'login'])->name('login'); // Ruta de login
    Route::post('register', [UserController::class, 'userRegister']); // Ruta de registro

    // Rutas protegidas por el middleware JWT
    Route::middleware('auth:api')->group(function () {
        Route::post('logout', [UserController::class, 'logout']);
        Route::get('me', [UserController::class, 'me']);
    });
});

