<?php

use App\Http\Controllers\api\authController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\postController;
use App\Http\Controllers\api\ukmController;

Route::post("/login", [authController::class, "login"]);
Route::post("/register", [authController::class, "register"]);

Route::get("/posts", [postController::class, "index"]);
Route::get("/post/{id}", [postController::class, "show"]);

Route::get("/ukms", [ukmController::class, "index"]);
