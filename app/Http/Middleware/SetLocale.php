<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($locale = Session::get('locale')) {
            App::setLocale($locale);
        } else if (Auth::check()) {
            $locale = auth()->user()->locale;
            Session::put('locale', $locale);
            App::setLocale($locale);
        }

        return $next($request);
    }
}
