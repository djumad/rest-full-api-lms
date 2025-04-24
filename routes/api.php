<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\UserMeedleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login' , [UserController::class , 'login']);

Route::middleware(UserMeedleware::class)->group(function(){
    Route::get("/users/me" , [UserController::class , 'getUser']);

    Route::middleware(AdminMiddleware::class)->group(function(){
        Route::get('/users/admin/guru', [AdminController::class , 'getAllGuru']);
        Route::get('/users/admin/siswa', [AdminController::class , 'getAllSiswa']);
        Route::post('/users/admin/create', [AdminController::class , 'createUser']);
    });
});
