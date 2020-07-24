<?php

namespace App\Http\Middleware;

use Closure;

use Illuminate\Support\Facades\App;

use Illuminate\Http\Request;

class HttpsProtocol {

    public function handle($request, Closure $next) {
        // if (!$request->secure() && App::environment() === 'production') {
        //     return redirect()->secure($request->getRequestUri());
        // }

        //$request->setTrustedProxies([$request->getClientIp()], Request::HEADER_X_FORWARDED_ALL);
        //Request::setTrustedProxies([$request->getClientIp()]);

        if (!$request->secure()) {
            return redirect()->secure($request->getRequestUri());
        }

        return $next($request);
    }
}

?>