<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::as('webhook.')->prefix('webhook')->group(function () {
    Route::post('/whatsapp', [\App\Http\Controllers\API\Webhook\WhatsAppController::class, 'handle'])->name('whatsapp');
});
