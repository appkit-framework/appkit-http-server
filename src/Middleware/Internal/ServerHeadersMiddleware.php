<?php

namespace AppKit\Http\Server\Middleware\Internal;

use AppKit\Http\Middleware\HttpMiddlewareInterface;

class ServerHeadersMiddleware implements HttpMiddlewareInterface {
    public function processRequest($request, $next) {
        $response = $next($request);

        if(! $response -> hasHeader('Server'))
            $response = $response -> withHeader('Server', 'AppKitHTTP');

        return $response;
    }
}
