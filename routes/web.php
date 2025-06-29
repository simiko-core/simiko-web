<?php

use App\Filament\UkmPanel\Resources\UnitKegiatanProfileResource;
use App\Models\UnitKegiatanProfile;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Public event registration routes
Route::get('/event/register/{token}', [App\Http\Controllers\EventRegistrationController::class, 'show'])
    ->name('event.register');

Route::post('/event/register/{token}', [App\Http\Controllers\EventRegistrationController::class, 'register']);

Route::get('/event/{token}/payment/{transactionId}', [App\Http\Controllers\EventRegistrationController::class, 'showPayment'])
    ->name('event.payment');

Route::post('/event/{token}/payment/{transactionId}/upload-proof', [App\Http\Controllers\EventRegistrationController::class, 'uploadProof'])
    ->name('event.upload-proof');

Route::get('/event/{token}/status/{transactionId}', [App\Http\Controllers\EventRegistrationController::class, 'status'])
    ->name('event.status');

Route::get('/event/{token}/receipt/{transactionId}', [App\Http\Controllers\EventRegistrationController::class, 'downloadReceipt'])
    ->name('event.download-receipt');
