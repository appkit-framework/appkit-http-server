<?php

namespace AppKit\Http\Server\Middleware\Internal;

use AppKit\Http\Server\Middleware\HttpServerMiddlewareInterface;

use AppKit\Http\Message\AbstractHttpResponseException;
use AppKit\Log\LogLevel;

class AccessLogMiddleware implements HttpServerMiddlewareInterface {
    private $log;

    function __construct($log) {
        $this -> log = $log;
    }

    public function processRequest($request, $next) {
        $response = $next($request);

        $message = '{remoteAddr} {method} {url} => {status}';
        if($respException = $request -> getAttribute(AbstractHttpResponseException::class))
            $message .= ': ' . $respException -> getMessage();

        $status = $response -> getStatus();

        $this -> log -> log(
            $status >= 500 ? LogLevel::Warning : LogLevel::Debug,
            $message,
            [
                'remoteAddr' => $request -> getRemoteAddr(),
                'method' => $request -> getMethod(),
                'url' => $request -> getUrl(),
                'status' => $status
            ]
        );

        return $response;
    }
}
