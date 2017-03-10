<?php

namespace App\Http\Middleware;

use Closure;
use Log;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            Log::debug("RedirectIfAuthenticated:: ".Auth::user()->role." can_uploads: ".Auth::user()->can('uploads'));
            if(Auth::user()->can('uploads'))return redirect('/panel');
            else return redirect('/panel/downloads');
        }

        return $next($request);
    }
}
