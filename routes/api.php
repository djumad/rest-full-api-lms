<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\GuruMiddleware;
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

    Route::middleware(GuruMiddleware::class)->group(function(){
        Route::get("/users/guru/tugas", [GuruController::class, 'getTugas']);
        Route::post("/users/guru/tugas", [GuruController::class, 'createTugas']);
        Route::put("/users/guru/tugas/{id}", [GuruController::class, 'updateTugas']);
        Route::delete("/users/guru/tugas/{id}", [GuruController::class, 'deleteTugas']);
    });
    
});
