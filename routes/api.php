<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

// Route::get('/users', function(Request $request){
//     return $request->user();
// })->middleware('auth:sanctum');

// Route::group(['middleware' => 'api'], function ($routes) {
//     Route::post('user-register', [UserController::class, 'userRegister']);
    
// });

Route::middleware('api')->prefix("v1/users")->group(function () {
    Route::post('register', [UserController::class, 'userRegister']);
});
