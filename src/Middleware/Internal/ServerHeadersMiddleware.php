<?php

namespace AppKit\Http\Server\Middleware\Internal;

use AppKit\Http\Server\Middleware\HttpServerMiddlewareInterface;

class ServerHeadersMiddleware implements HttpServerMiddlewareInterface {
    public function processRequest($request, $next) {
        $response = $next($request);

        if(! $response -> hasHeader('Server'))
            $response = $response -> withHeader('Server', 'AppKitHTTP');

        return $response;
    }
}
