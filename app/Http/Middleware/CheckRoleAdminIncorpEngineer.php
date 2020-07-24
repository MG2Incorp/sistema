<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckRoleAdminIncorpEngineer
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
        if (Auth::check() && !in_array(Auth::user()->role, ['ADMIN', 'INCORPORATOR', 'ENGINEER'])) {
            return redirect()->route('home')->with('error', 'Você não permissão necessária para acessar essa área.');
        }

        return $next($request);
    }
}
