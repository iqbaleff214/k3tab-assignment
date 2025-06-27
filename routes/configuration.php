<?php

use Illuminate\Support\Facades\Route;

Route::name('configuration.')->prefix('configuration')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [\App\Http\Controllers\ConfigurationController::class, 'index'])->name('index');

    Route::get('whatsapp', [\App\Http\Controllers\Configuration\WhatsAppController::class, 'index'])->name('whatsapp.index')->middleware(\App\Enum\Permission::ViewWhatsApp->asMiddleware());
    Route::post('whatsapp', [\App\Http\Controllers\Configuration\WhatsAppController::class, 'store'])->name('whatsapp.store')->middleware(\App\Enum\Permission::AddWhatsApp->asMiddleware());
    Route::post('whatsapp/{token}/connect', [\App\Http\Controllers\Configuration\WhatsAppController::class, 'connect'])->name('whatsapp.connect')->middleware(\App\Enum\Permission::EditWhatsApp->asMiddleware());
    Route::post('whatsapp/{token}/disconnect', [\App\Http\Controllers\Configuration\WhatsAppController::class, 'disconnect'])->name('whatsapp.disconnect')->middleware(\App\Enum\Permission::EditWhatsApp->asMiddleware());
    Route::delete('whatsapp/{id}', [\App\Http\Controllers\Configuration\WhatsAppController::class, 'destroy'])->name('whatsapp.destroy')->middleware(\App\Enum\Permission::DeleteWhatsApp->asMiddleware());

    Route::get('approval', [\App\Http\Controllers\Configuration\ApprovalFlowController::class, 'index'])->name('approval-flow.index')->middleware(\App\Enum\Permission::ViewApprovalFlow->asMiddleware());
    Route::post('approval', [\App\Http\Controllers\Configuration\ApprovalFlowController::class, 'store'])->name('approval-flow.store')->middleware(\App\Enum\Permission::AddApprovalFlow->asMiddleware());
    Route::put('approval/{approval}', [\App\Http\Controllers\Configuration\ApprovalFlowController::class, 'update'])->name('approval-flow.update')->middleware(\App\Enum\Permission::EditApprovalFlow->asMiddleware());
    Route::delete('approval/{approval}', [\App\Http\Controllers\Configuration\ApprovalFlowController::class, 'destroy'])->name('approval-flow.destroy')->middleware(\App\Enum\Permission::DeleteApprovalFlow->asMiddleware());
});
