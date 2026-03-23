<?php

namespace AppKit\Http\Server\Middleware\Internal;

use AppKit\Http\Server\Middleware\HttpServerMiddlewareInterface;

class RequestIdMiddleware implements HttpServerMiddlewareInterface {
    private $log;

    function __construct($log) {
        $this -> log = $log;
    }

    public function processRequest($request, $next) {
        $requestId = bin2hex(random_bytes(4));
        $request -> setAttribute('requestId', $requestId);
        $this -> log -> setContext('requestId', $requestId);

        return $next($request);
    }
}
