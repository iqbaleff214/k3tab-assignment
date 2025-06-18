<?php

use Illuminate\Support\Facades\Route;

Route::name('configuration.')->prefix('configuration')->middleware(['auth', 'verified'])->group(function () {
    Route::redirect('/', '/configuration/whatsapp');

    Route::get('whatsapp', [\App\Http\Controllers\Configuration\WhatsAppController::class, 'index'])->name('whatsapp.index')->middleware(\App\Enum\Permission::ViewWhatsApp->asMiddleware());
    Route::post('whatsapp', [\App\Http\Controllers\Configuration\WhatsAppController::class, 'store'])->name('whatsapp.store')->middleware(\App\Enum\Permission::AddWhatsApp->asMiddleware());
    Route::post('whatsapp/{token}/connect', [\App\Http\Controllers\Configuration\WhatsAppController::class, 'connect'])->name('whatsapp.connect')->middleware(\App\Enum\Permission::EditWhatsApp->asMiddleware());
    Route::post('whatsapp/{token}/disconnect', [\App\Http\Controllers\Configuration\WhatsAppController::class, 'disconnect'])->name('whatsapp.disconnect')->middleware(\App\Enum\Permission::EditWhatsApp->asMiddleware());
    Route::delete('whatsapp/{id}', [\App\Http\Controllers\Configuration\WhatsAppController::class, 'destroy'])->name('whatsapp.destroy')->middleware(\App\Enum\Permission::DeleteWhatsApp->asMiddleware());
});
