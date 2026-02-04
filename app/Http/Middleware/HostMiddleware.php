<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// My code starts here
class HostMiddleware
{
    
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated and has the 'host' role
        if (!auth()->check() || !auth()->user()->role || auth()->user()->role->name !== 'Host') {
            return redirect('/')->with('error', 'Access denied. Host privileges required.');
        }

        return $next($request);
    }
}