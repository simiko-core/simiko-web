<?php

use App\Http\Controllers\api\authController;
use App\Http\Controllers\api\eventsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\postController;
use App\Http\Controllers\api\ukmController;

Route::post("/login", [authController::class, "login"]);
Route::post("/register", [authController::class, "register"]);

Route::get("/posts", [postController::class, "index"]);
Route::get("/post/{id}", [postController::class, "show"]);

Route::get("/ukms", [ukmController::class, "index"]);

Route::get("/events", [eventsController::class, "index"]);
Route::get("/event/{id}", [eventsController::class, "show"]);
