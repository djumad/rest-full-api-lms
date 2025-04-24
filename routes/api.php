<?php

use App\Http\Controllers\UserController;
use App\Http\Middleware\UserMeedleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login' , [UserController::class , 'login']);

Route::middleware(UserMeedleware::class)->group(function(){
    Route::get("/users/me" , [UserController::class , 'getUser']);
});
