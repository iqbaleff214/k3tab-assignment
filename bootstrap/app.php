<?php

use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->encryptCookies(except: ['appearance', 'sidebar_state']);

        $middleware->web(append: [
            HandleAppearance::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
            \App\Http\Middleware\SetLocale::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Illuminate\Auth\Access\AuthorizationException $e, \Illuminate\Http\Request $request) {
            return \Inertia\Inertia::render('Error', [
                'user' => \Illuminate\Support\Facades\Auth::user(),
                'appName' => config('app.name'),
                'statusCode' => 401,
                'message' => $e->getMessage(),
            ]);
        });
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, \Illuminate\Http\Request $request) {
            return \Inertia\Inertia::render('Error', [
                'user' => \Illuminate\Support\Facades\Auth::user(),
                'appName' => config('app.name'),
                'statusCode' => $e->getStatusCode(),
                'message' => $e->getMessage(),
            ]);
        });
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException $e, \Illuminate\Http\Request $request) {
            return \Inertia\Inertia::render('Error', [
                'user' => \Illuminate\Support\Facades\Auth::user(),
                'appName' => config('app.name'),
                'statusCode' => $e->getStatusCode(),
                'message' => $e->getMessage(),
            ]);
        });
        $exceptions->render(function (\Illuminate\Database\Eloquent\ModelNotFoundException $e, \Illuminate\Http\Request $request) {
            return \Inertia\Inertia::render('Error', [
                'user' => \Illuminate\Support\Facades\Auth::user(),
                'appName' => config('app.name'),
                'statusCode' => 404,
                'message' => $e->getMessage(),
            ]);
        });
        $exceptions->render(function (\Illuminate\Http\Exceptions\ThrottleRequestsException $e, \Illuminate\Http\Request $request) {
            return \Inertia\Inertia::render('Error', [
                'user' => \Illuminate\Support\Facades\Auth::user(),
                'appName' => config('app.name'),
                'statusCode' => $e->getStatusCode(),
                'message' => $e->getMessage(),
            ]);
        });
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, \Illuminate\Http\Request $request) {
            return \Inertia\Inertia::render('Error', [
                'user' => \Illuminate\Support\Facades\Auth::user(),
                'appName' => config('app.name'),
                'statusCode' => $e->getStatusCode(),
                'message' => $e->getMessage(),
            ]);
        });
    })->create();
