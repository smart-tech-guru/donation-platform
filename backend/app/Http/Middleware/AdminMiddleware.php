<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        if (!$user || !$user->hasRole('admin')) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        return $next($request);
    }
}
