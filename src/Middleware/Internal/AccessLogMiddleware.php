<?php

namespace AppKit\Http\Server\Middleware\Internal;

use AppKit\Http\Server\Middleware\ServerHttpMiddlewareInterface;

use AppKit\Log\LogLevel;

class AccessLogMiddleware implements ServerHttpMiddlewareInterface {
    private $log;

    function __construct($log) {
        $this -> log = $log;
    }

    public function processRequest($request, $next) {
        $response = $next($request);

        $status = $response -> getStatus();
        $responseException = $request -> getAttribute('responseException');
        $this -> log -> log(
            $status >= 500 ? LogLevel::Warning : LogLevel::Debug,
            '{remoteAddr} {method} {target} => {status} {message}',
            [
                'remoteAddr' => $request -> getRemoteAddr(),
                'method' => $request -> getMethod(),
                'target' => $request -> getOriginalTarget(),
                'status' => $status,
                'message' => $responseException ?-> getMessage() ?? ''
            ],

        );

        return $response;
    }
}
