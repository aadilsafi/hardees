<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class isAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is not logged in or does not have the right role
        if (!Auth::check() || !in_array(Auth::user()->role, ['super', 'admin'])) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
