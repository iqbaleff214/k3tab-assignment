<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', fn () => Inertia::render('Welcome', [
    'canRegister' => Route::has('register'),
]))->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', fn () => Inertia::render('Dashboard') )->name('dashboard');

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

    Route::name('json.')->prefix('json')->group(function () {
        Route::get('/activity-log/{module}/{id}', [\App\Http\Controllers\JSON\ActivityLogController::class, 'index'])->name('activity-log.index');
    });
});


require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
