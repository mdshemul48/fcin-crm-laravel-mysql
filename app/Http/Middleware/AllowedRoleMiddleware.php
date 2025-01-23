<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AllowedRoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role)
    {
        if (auth()->check() && auth()->user()->role === $role) {
            return $next($request);
        }

        return redirect()->route("dashboard")->with('error', 'You are not allowed to access this page.');
    }
}