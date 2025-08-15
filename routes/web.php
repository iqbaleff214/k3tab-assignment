<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', fn () => Inertia::render('Welcome', [
    'canRegister' => Route::has('register'),
]))->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', fn () => Inertia::render('Dashboard') )->name('dashboard');

    Route::get('/assessor', [\App\Http\Controllers\AssessorController::class, 'index'])->name('assessor.index');
    Route::get('/assessor/{assessor}', [\App\Http\Controllers\AssessorController::class, 'show'])->name('assessor.show');
    Route::post('/assessor', [\App\Http\Controllers\AssessorController::class, 'store'])->name('assessor.store');
    Route::put('/assessor/{assessor}', [\App\Http\Controllers\AssessorController::class, 'update'])->name('assessor.update');
    Route::delete('/assessor', [\App\Http\Controllers\AssessorController::class, 'massDestroy'])->name('assessor.mass-destroy');
    Route::delete('/assessor/{assessor}', [\App\Http\Controllers\AssessorController::class, 'destroy'])->name('assessor.destroy');

    Route::get('/assessee', [\App\Http\Controllers\AssesseeController::class, 'index'])->name('assessee.index');
    Route::get('/assessee/{assessee}', [\App\Http\Controllers\AssesseeController::class, 'show'])->name('assessee.show');
    Route::post('/assessee', [\App\Http\Controllers\AssesseeController::class, 'store'])->name('assessee.store');
    Route::put('/assessee/{assessee}', [\App\Http\Controllers\AssesseeController::class, 'update'])->name('assessee.update');
    Route::delete('/assessee', [\App\Http\Controllers\AssesseeController::class, 'massDestroy'])->name('assessee.mass-destroy');
    Route::delete('/assessee/{assessee}', [\App\Http\Controllers\AssesseeController::class, 'destroy'])->name('assessee.destroy');

    Route::get('/user', [\App\Http\Controllers\UserController::class, 'index'])->name('user.index')->middleware(\App\Enum\Permission::ViewUser->asMiddleware());
    Route::get('/user/{user}', [\App\Http\Controllers\UserController::class, 'show'])->name('user.show')->middleware(\App\Enum\Permission::ViewUser->asMiddleware());
    Route::post('/user', [\App\Http\Controllers\UserController::class, 'store'])->name('user.store')->middleware(\App\Enum\Permission::AddUser->asMiddleware());
    Route::put('/user/{user}', [\App\Http\Controllers\UserController::class, 'update'])->name('user.update')->middleware(\App\Enum\Permission::EditUser->asMiddleware());
    Route::delete('/user', [\App\Http\Controllers\UserController::class, 'massDestroy'])->name('user.mass-destroy')->middleware(\App\Enum\Permission::DeleteUser->asMiddleware());
    Route::delete('/user/{user}', [\App\Http\Controllers\UserController::class, 'destroy'])->name('user.destroy')->middleware(\App\Enum\Permission::DeleteUser->asMiddleware());

    Route::get('/role', [\App\Http\Controllers\RoleController::class, 'index'])->name('role.index')->middleware(\App\Enum\Permission::ViewRole->asMiddleware());
    Route::post('/role', [\App\Http\Controllers\RoleController::class, 'store'])->name('role.store')->middleware(\App\Enum\Permission::AddRole->asMiddleware());
    Route::put('/role/{role}', [\App\Http\Controllers\RoleController::class, 'update'])->name('role.update')->middleware(\App\Enum\Permission::EditRole->asMiddleware());
    Route::delete('/role', [\App\Http\Controllers\RoleController::class, 'massDestroy'])->name('role.mass-destroy')->middleware(\App\Enum\Permission::DeleteRole->asMiddleware());
    Route::delete('/role/{role}', [\App\Http\Controllers\RoleController::class, 'destroy'])->name('role.destroy')->middleware(\App\Enum\Permission::DeleteRole->asMiddleware());

    Route::get('/notification', [\App\Http\Controllers\Settings\NotificationController::class, 'index'])->name('notification.index');
    Route::post('/notification', [\App\Http\Controllers\Settings\NotificationController::class, 'markAsReadAll'])->name('notification.mark-as-read-all');
    Route::post('/notification/{id}', [\App\Http\Controllers\Settings\NotificationController::class, 'markAsRead'])->name('notification.mark-as-read');
    Route::delete('/notification/{id}', [\App\Http\Controllers\Settings\NotificationController::class, 'destroy'])->name('notification.destroy');
});


require __DIR__.'/configuration.php';
require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
require __DIR__.'/json.php';
