<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $permission
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $permission)
    {
        if (Auth::guest()) {
            return response()->unauthorized('You must be logged in to access this resource');
        }

        if (!Auth::user()->hasPermissionTo($permission)) {
            return response()->forbidden('You do not have permission to access this resource');
        }

        return $next($request);
    }
}

