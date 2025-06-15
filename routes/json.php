<?php

use Illuminate\Support\Facades\Route;

Route::name('json.')->prefix('json')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/notification', [\App\Http\Controllers\Json\NotificationController::class, 'index'])->name('notification.index');

    Route::get('/activity-log/{module}/{id}', [\App\Http\Controllers\JSON\ActivityLogController::class, 'index'])->name('activity-log.index');
});
