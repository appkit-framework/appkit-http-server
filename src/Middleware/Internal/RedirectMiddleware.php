<?php

namespace AppKit\Http\Server\Middleware\Internal;

use AppKit\Http\Server\Middleware\HttpServerMiddlewareInterface;

use AppKit\Http\Message\AbsoluteHttpRedirect;
use AppKit\Http\Message\HttpResponse;

class RedirectMiddleware implements HttpServerMiddlewareInterface {
    public function processRequest($request, $next) {
        try {
            return $next($request);
        } catch(AbsoluteHttpRedirect $e) {
            $setReason = $request -> getAttribute(AccessLogMiddleware::class)['setReason'];
            $setReason('Redirect to ' . $e -> getLocation());

            return new HttpResponse(
                $e -> getCode(),
                $e -> getHeaders()
            );
        }
    }

}
