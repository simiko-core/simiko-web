<?php

use App\Http\Controllers\api\authController;
use App\Http\Controllers\api\ukmController;
use App\Http\Controllers\api\bannerController;
use App\Http\Controllers\api\feedController;
use App\Http\Controllers\api\paymentController;

Route::post("/login", [authController::class, "login"]);
Route::post("/register", [authController::class, "register"]);

// Protected routes (authentication required)
Route::middleware("auth:sanctum")->group(function () {
    Route::post("/logout", [authController::class, "logout"]);

    Route::get("/ukms", [ukmController::class, "index"]);

    // UKM registration
    Route::post("/ukm/{id}/register", [ukmController::class, "registerMember"]);

    // UKM profile
    Route::get("/ukm/{id}/profile", [ukmController::class, "profile"]);

    // Banner
    Route::get("/banner", [bannerController::class, "index"]);

    // User profile
    Route::get("/user/profile", [authController::class, "profile"]);

    // Search UKM by name
    Route::get("/ukms/search", [ukmController::class, "search"]);

    // New route for full UKM profile
    Route::get('/ukm/{id}/profile-full', [App\Http\Controllers\api\ukmController::class, 'profileFull']);

    // Unified Feed endpoints
    Route::get("/feed", [feedController::class, "index"]);
    Route::get("/feed/{id}", [feedController::class, "show"]);
    Route::get("/posts", [feedController::class, "posts"]);
    Route::get("/events", [feedController::class, "events"]);

    // Payment endpoints
    Route::get("/payment/configurations", [paymentController::class, "getConfigurations"]);
    Route::post("/payment/create-transaction", [paymentController::class, "createTransaction"]);
    Route::get("/payment/transactions", [paymentController::class, "getUserTransactions"]);
    Route::post("/payment/transactions/{transactionId}/upload-proof", [paymentController::class, "uploadProof"]);
});
