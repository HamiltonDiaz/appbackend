<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MailSettingController;

Route::prefix("v1/users")->group(function () {
    Route::post('login', [UserController::class, 'login'])->name('login'); // Ruta de login
    Route::post('register', [UserController::class, 'userRegister']); // Ruta de registro
    Route::post('password/forgot', [UserController::class, 'sendResetLinkEmail'])->name('password.forgot');// Ruta para enviar el enlace de recuperación de contraseña
    Route::post('password/reset', [UserController::class, 'reset'])->name('password.reset');// Ruta para restablecer la contraseña

      

    // Rutas protegidas por el middleware JWT
    Route::middleware('auth:api')->group(function () {
        Route::post('logout', [UserController::class, 'logout']);
        Route::get('me', [UserController::class, 'me']);    
        Route::get('find/{id}', [UserController::class, 'findById']);    
        Route::get('list-all/{rows?}', [UserController::class, 'index']);   

        
    });
});


Route::prefix("v1/config-server")->group(function () {
    Route::middleware('auth:api')->group(function () {
        Route::post('email', [MailSettingController::class, 'store']);  
    });
});