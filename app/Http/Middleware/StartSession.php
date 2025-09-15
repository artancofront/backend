<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;

class StartSession
{
    public function handle($request, Closure $next)
    {
        if (Session::isStarted() === false) {
            Session::start();
        }

        return $next($request);
    }
}
