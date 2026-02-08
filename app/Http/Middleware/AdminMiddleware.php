<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated and has the 'Admin' role
        if (!auth()->check() || !auth()->user()->role || auth()->user()->role->name !== 'Admin') {
            return redirect('/admin')->with('error', 'Access denied. Admin privileges required.');
        }

        return $next($request);
    }
}
