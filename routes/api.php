<?php

use App\Http\Controllers\api\authController;
use App\Http\Controllers\api\eventsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\postController;
use App\Http\Controllers\api\ukmController;

Route::post("/login", [authController::class, "login"]);
Route::post("/register", [authController::class, "register"]);

// Protected routes (authentication required)
Route::middleware("auth:sanctum")->group(function () {
    Route::post("/logout", [authController::class, "logout"]);

    Route::get("/posts", [postController::class, "index"]);
    Route::get("/post/{id}", [postController::class, "show"]);
    Route::get("/ukms", [ukmController::class, "index"]);
    Route::get("/events", [eventsController::class, "index"]);
    Route::get("/event/{id}", [eventsController::class, "show"]);

    // UKM member registration requires authentication
    Route::post("/ukm/{id}/register", [ukmController::class, "registerMember"]);

    // Profile UKM
    Route::get("/ukm/{id}/profile", [ukmController::class, "profile"]);

    // Add user profile endpoint for checking current user data
    // Route::get("/user/profile", [authController::class, "profile"]);
});
