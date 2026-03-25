<?php

namespace AppKit\Http\Server\Middleware\Internal;

use AppKit\Http\Server\Middleware\ServerHttpMiddlewareInterface;

class ServerHeadersMiddleware implements ServerHttpMiddlewareInterface {
    public function processRequest($request, $next) {
        $response = $next($request);

        if(! $response -> hasHeader('Server'))
            $response -> setHeader('Server', 'AppKitHTTP');

        return $response;
    }
}
