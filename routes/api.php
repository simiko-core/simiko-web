<?php

use App\Http\Controllers\api\authController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\postController;

Route::post("/login", [authController::class, "login"]);
Route::post("/register", [authController::class, "register"]);

Route::apiResource("/post", postController::class);
