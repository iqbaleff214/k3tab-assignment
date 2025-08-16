<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserType
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next, string ...$types): Response
    {
        $type = (string) $request->user()->type;
        if (!in_array($type, $types)) {
            abort(403);
        }

        return $next($request);
    }
}
