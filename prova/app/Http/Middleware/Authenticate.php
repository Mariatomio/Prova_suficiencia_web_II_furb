<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Authenticate
{
    public function handle(Request $request, Closure $next, ...$guards)
    {
        if (!Auth::guard($guards[0] ?? 'api')->check()) {
            return response()->json(['message' => 'Não autorizado'], 401);
        }

        return $next($request);
    }
}