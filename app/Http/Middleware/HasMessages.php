<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class HasMessages
{
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::check() && Auth::user()->messages_not_read->count()) {
            return redirect()->route('messages.read');
        }

        return $next($request);
    }
}
