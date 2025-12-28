<?php

namespace AppKit\Http\Server\Middleware\Internal;

use AppKit\Http\Middleware\HttpMiddlewareInterface;
use AppKit\Http\Message\HttpRedirect;
use AppKit\Http\Message\HttpResponse;

class RedirectMiddleware implements HttpMiddlewareInterface {
    public function processRequest($request, $next) {
        try {
            return $next($request);
        } catch(HttpRedirect $e) {
            $setReason = $request -> getAttribute(AccessLogMiddleware::class)['setReason'];
            $setReason('Redirect to ' . $e -> getLocation());

            return new HttpResponse(
                $e -> getCode(),
                $e -> getHeaders()
            );
        }
    }

}
