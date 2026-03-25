<?php

namespace AppKit\Http\Server\Middleware\Internal;

use AppKit\Http\Server\Middleware\HttpServerMiddlewareInterface;

use AppKit\Http\Server\Message\ServerHttpRedirect;

class RedirectMiddleware implements HttpServerMiddlewareInterface {
    public function processRequest($request, $next) {
        try {
            return $next($request);
        } catch(ServerHttpRedirect $e) {
            $request -> setAttribute('responseException', $e);
            return $e -> getResponse();
        }
    }

}
