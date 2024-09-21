<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Users\UserController;
use App\Http\Controllers\Admin\Config\MailSettingController;
use App\Http\Controllers\Admin\Users\RolController;

Route::prefix("v1/users")->group(function () {
    Route::post('login', [UserController::class, 'login'])->name('login'); // Ruta de login
    Route::post('register', [UserController::class, 'userRegister'])->name('register'); // Ruta de registro
    Route::post('password/forgot', [UserController::class, 'sendResetLinkEmail'])->name('password.forgot');// Ruta para enviar el enlace de recuperación de contraseña
    Route::post('password/reset', [UserController::class, 'reset'])->name('password.reset');// Ruta para restablecer la contraseña

    // Rutas protegidas por el middleware JWT
    Route::middleware('auth:api')->group(function () {
        Route::post('logout', [UserController::class, 'logout'])->name('user.logout');
        Route::get('me', [UserController::class, 'me'])->name('user.me');    
        Route::get('find/{id}', [UserController::class, 'findById'])->name('user.findById');    
        Route::get('list-all/{rows?}', [UserController::class, 'user.index']);
        
        //Roles
    });

    Route::get('rol/list-all/{rows?}', [RolController::class, 'index'])->name('user.rol.index');
    Route::post('rol/assing', [RolController::class, 'store'])->name('user.rol.assing');
});


Route::prefix("v1/config-server")->group(function () {
    Route::middleware('auth:api')->group(function () {
        Route::post('email', [MailSettingController::class, 'store'])->name('config-server.store');  
    });
});