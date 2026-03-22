<?php

namespace AppKit\Http\Server\Middleware\Internal;

use AppKit\Http\Server\Middleware\HttpServerMiddlewareInterface;

use AppKit\Http\Message\HttpRedirect;
use AppKit\Http\Message\AbstractHttpResponseException;
use AppKit\Http\Server\Message\ServerHttpResponse;

class RedirectMiddleware implements HttpServerMiddlewareInterface {
    public function processRequest($request, $next) {
        try {
            return $next($request);
        } catch(HttpRedirect $e) {
            $request -> setAttribute(AbstractHttpResponseException::class, $e);

            return new ServerHttpResponse(
                $e -> getStatus(),
                $e -> getHeaders()
            );
        }
    }

}
