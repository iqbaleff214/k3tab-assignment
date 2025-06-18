<?php

use Illuminate\Support\Facades\Route;

Route::name('configuration.')->prefix('configuration')->middleware(['auth', 'verified'])->group(function () {
    Route::redirect('/', '/configuration/whatsapp');


    Route::get('whatsapp', [\App\Http\Controllers\Configuration\WhatsAppController::class, 'index'])->name('whatsapp.index');
    Route::post('whatsapp', [\App\Http\Controllers\Configuration\WhatsAppController::class, 'store'])->name('whatsapp.store');
    Route::post('whatsapp/{token}/connect', [\App\Http\Controllers\Configuration\WhatsAppController::class, 'connect'])->name('whatsapp.connect');
    Route::post('whatsapp/{token}/disconnect', [\App\Http\Controllers\Configuration\WhatsAppController::class, 'disconnect'])->name('whatsapp.disconnect');
    Route::delete('whatsapp/{id}', [\App\Http\Controllers\Configuration\WhatsAppController::class, 'destroy'])->name('whatsapp.destroy');
});
