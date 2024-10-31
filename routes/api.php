<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Users\UserController;
use App\Http\Controllers\Admin\Config\MailSettingController;
use App\Http\Controllers\Admin\Users\RolController;
use App\Http\Controllers\Project\projectController;

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
        Route::get('list-all/{rows?}', [UserController::class, 'index'])->name('user.index');
        Route::put('update', [UserController::class, 'updateUser'])->name('user.update');
        Route::delete('delete/{id}', [UserController::class, 'destroy'])->name('user.delete');
        
        //Roles
    });

    Route::get('rol/list-all/{rows?}', [RolController::class, 'index'])->name('user.rol.index');
    Route::post('rol/create', [RolController::class, 'store'])->name('user.rol.create');
    Route::post('rol/assing', [RolController::class, 'asingRole'])->name('user.rol.assing');
});


//Rutas para configuración del servidor
Route::prefix("v1/config-server")->group(function () {
    Route::middleware('auth:api')->group(function () {
        Route::post('email', [MailSettingController::class, 'store'])->name('config-server.store');  
    });
});

//Rutas para gestión de proyectos
Route::prefix("v1/project")->group(function () {
    Route::middleware('auth:api')->group(function () {
        Route::post('create', [projectController::class, 'store'])->name('project.create');
        Route::get('list-all/{rows?}/{search?}', [projectController::class, 'index'])->name('project.index');
        Route::get('find/{id}', [projectController::class, 'findById'])->name('project.findById');
        Route::put('update', [projectController::class, 'update'])->name('project.update');
        Route::get('list-all/{rows?}', [projectController::class, 'index'])->name('project.index');
        Route::delete('delete/{id}', [projectController::class, 'destroy'])->name('project.delete');
        Route::get('download/{file}', [projectController::class, 'downloadFile'])->name('project.downloadfile');
        Route::post('assign-member', [projectController::class, 'assignMember'])->name('project.assignMember');
    });
    
});