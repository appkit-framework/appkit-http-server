<?php

namespace AppKit\Http\Server\Middleware\Internal;

use AppKit\Http\Middleware\HttpMiddlewareInterface;

class AccessLogMiddleware implements HttpMiddlewareInterface {
    private $log;

    function __construct($log) {
        $this -> log = $log;
    }

    public function processRequest($request, $next) {
        $reason = null;
        $request = $request -> withAttribute(
            self::class,
            [ 'setReason' => function($newReason) use(&$reason) { $reason = $newReason; } ]
        );

        $response = $next($request);

        $remoteAddr = $request -> getHeaderLine('X-Forwarded-For');
        if(empty($remoteAddr))
            $remoteAddr = $request -> getServerParams()['REMOTE_ADDR'];
        $method = $request -> getMethod();
        $uri = $request -> getUri();
        $path = $uri -> getPath();
        $query = $uri -> getQuery();
        $fragment = $uri -> getFragment();
        if($query != '')
            $path .= '?' . $query;
        if($fragment != '')
            $uri .= '#' . $fragment;

        $status = $response -> getStatusCode();

        $message = "$remoteAddr $method $path => $status";
        if($reason)
            $message .= ": $reason";
        $this -> log -> debug($message);

        return $response;
    }
}
