<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // We use the helper 'session()' instead of the Facade 'Session::'
        // to avoid import errors.
        if (session()->has('locale')) {
            App::setLocale(session()->get('locale'));
        }

        return $next($request);
    }
}