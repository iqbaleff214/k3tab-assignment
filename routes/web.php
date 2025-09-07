<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', fn () => Inertia::render('Welcome', [
    'canRegister' => Route::has('register'),
]))->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\HomeController::class, 'dashboard'])->name('dashboard');

    Route::middleware('allowed-type:admin')->group(function () {
        Route::prefix('a')->name('admin.')->group(function () {
            Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

            Route::get('/module', [\App\Http\Controllers\ModuleController::class, 'index'])->name('module.index');
            Route::get('/module/{module}', [\App\Http\Controllers\ModuleController::class, 'show'])->name('module.show');
            Route::post('/module', [\App\Http\Controllers\ModuleController::class, 'store'])->name('module.store');
            Route::put('/module/{module}', [\App\Http\Controllers\ModuleController::class, 'update'])->name('module.update');
            Route::delete('/module', [\App\Http\Controllers\ModuleController::class, 'massDestroy'])->name('module.mass-destroy');
            Route::delete('/module/{module}', [\App\Http\Controllers\ModuleController::class, 'destroy'])->name('module.destroy');
            Route::post('/module/{module}/media', [\App\Http\Controllers\ModuleController::class, 'uploadFile'])->name('module.upload-file');
            Route::delete('/module/{module}/media/{media}', [\App\Http\Controllers\ModuleController::class, 'removeFile'])->name('module.remove-file');

            Route::post('/module/{module}/question', [\App\Http\Controllers\ModuleQuestionController::class, 'store'])->name('module.question.store');
            Route::put('/module/{module}/question/{question}', [\App\Http\Controllers\ModuleQuestionController::class, 'update'])->name('module.question.update');
            Route::delete('/module/{module}/question', [\App\Http\Controllers\ModuleQuestionController::class, 'massDestroy'])->name('module.question.mass-destroy');
            Route::delete('/module/{module}/question/{question}', [\App\Http\Controllers\ModuleQuestionController::class, 'destroy'])->name('module.question.destroy');

            Route::get("/performance-guide", [\App\Http\Controllers\PerformanceGuideController::class, 'index'])->name('performance-guide.index');
            Route::post("/performance-guide", [\App\Http\Controllers\PerformanceGuideController::class, 'store'])->name('performance-guide.store');
            Route::get("/performance-guide/{guide:skill_number}/print", [\App\Http\Controllers\PerformanceGuideController::class, 'print'])->name('performance-guide.print');
            Route::put("/performance-guide/{guide}", [\App\Http\Controllers\PerformanceGuideController::class, 'update'])->name('performance-guide.update');
            Route::delete("/performance-guide/{guide}", [\App\Http\Controllers\PerformanceGuideController::class, 'destroy'])->name('performance-guide.destroy');

            Route::get("/assessment", [\App\Http\Controllers\AssessmentController::class, 'index'])->name('assessment.index');
            Route::delete("/assessment/{assessment}", [\App\Http\Controllers\AssessmentController::class, 'destroy'])->name('assessment.destroy');

            Route::get("/post-test", [\App\Http\Controllers\PostTestController::class, 'index'])->name('post-test.index');
            Route::delete("/post-test/{test}", [\App\Http\Controllers\PostTestController::class, 'destroy'])->name('post-test.destroy');
        });

        Route::get('/assessor', [\App\Http\Controllers\AssessorController::class, 'index'])->name('assessor.index');
        Route::get('/assessor/{assessor}', [\App\Http\Controllers\AssessorController::class, 'show'])->name('assessor.show');
        Route::post('/assessor', [\App\Http\Controllers\AssessorController::class, 'store'])->name('assessor.store');
        Route::put('/assessor/{assessor}', [\App\Http\Controllers\AssessorController::class, 'update'])->name('assessor.update');
        Route::delete('/assessor', [\App\Http\Controllers\AssessorController::class, 'massDestroy'])->name('assessor.mass-destroy');
        Route::delete('/assessor/{assessor}', [\App\Http\Controllers\AssessorController::class, 'destroy'])->name('assessor.destroy');
    });

    Route::middleware('allowed-type:admin,assessor')->group(function () {
        Route::get('/assessee', [\App\Http\Controllers\AssesseeController::class, 'index'])->name('assessee.index');
        Route::get('/assessee/{assessee}', [\App\Http\Controllers\AssesseeController::class, 'show'])->name('assessee.show');
        Route::post('/assessee', [\App\Http\Controllers\AssesseeController::class, 'store'])->name('assessee.store');
        Route::put('/assessee/{assessee}', [\App\Http\Controllers\AssesseeController::class, 'update'])->name('assessee.update');
        Route::delete('/assessee', [\App\Http\Controllers\AssesseeController::class, 'massDestroy'])->name('assessee.mass-destroy');
        Route::delete('/assessee/{assessee}', [\App\Http\Controllers\AssesseeController::class, 'destroy'])->name('assessee.destroy');
    });

    Route::middleware('allowed-type:assessor')->prefix('r')->name('assessor.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Assessor\DashboardController::class, 'index'])->name('dashboard');

        Route::get('/assessment/{assessment}/print', [\App\Http\Controllers\Assessor\AssessmentController::class, 'print'])->name('assessment.print');
        Route::post('/assessment/{assessment}/print', [\App\Http\Controllers\Assessor\AssessmentController::class, 'downloadPrint'])->name('assessment.download-print');
        Route::post('/assessment/{assessment}', [\App\Http\Controllers\Assessor\AssessmentController::class, 'store'])->name('assessment.store');
        Route::post('/assessment/{assessment}/proposal', [\App\Http\Controllers\Assessor\AssessmentController::class, 'proposal'])->name('assessment.proposal');
    });

    Route::middleware('allowed-type:assessee')->prefix('e')->name('assessee.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Assessee\DashboardController::class, 'index'])->name('dashboard');

        Route::get('/module', [\App\Http\Controllers\Assessee\ModuleController::class, 'index'])->name('module.index');
        Route::get('/module/{module}', [\App\Http\Controllers\Assessee\ModuleController::class, 'show'])->name('module.show');
        Route::post('/module/{module}/download/{media}', [\App\Http\Controllers\Assessee\ModuleController::class, 'download'])->name('module.download');

        Route::post('/module/{module}/start', [\App\Http\Controllers\Assessee\ModuleController::class, 'startPostTest'])->name('module.post-test.start');
        Route::post('/module/{module}/finish', [\App\Http\Controllers\Assessee\ModuleController::class, 'finishPostTest'])->name('module.post-test.finish');
        Route::post('/module/{module}/cancel', [\App\Http\Controllers\Assessee\ModuleController::class, 'cancelPostTest'])->name('module.post-test.cancel');

        Route::get('/assessment', [\App\Http\Controllers\Assessee\AssessmentController::class, 'index'])->name('assessment.index');
        Route::get('/assessment/schedule', [\App\Http\Controllers\Assessee\AssessmentController::class, 'schedule'])->name('assessment.schedule');
        Route::get('/assessment/{assessment}', [\App\Http\Controllers\Assessee\AssessmentController::class, 'show'])->name('assessment.show');
        Route::get('/assessment/{assessment}/print', [\App\Http\Controllers\Assessee\AssessmentController::class, 'print'])->name('assessment.print');
        Route::post('/assessment/{assessment}/print', [\App\Http\Controllers\Assessee\AssessmentController::class, 'downloadPrint'])->name('assessment.download-print');
        Route::post('/assessment', [\App\Http\Controllers\Assessee\AssessmentController::class, 'store'])->name('assessment.store');
        Route::delete('/assessment/{assessment}', [\App\Http\Controllers\Assessee\AssessmentController::class, 'destroy'])->name('assessment.destroy');
    });
});


require __DIR__.'/configuration.php';
require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
require __DIR__.'/json.php';
