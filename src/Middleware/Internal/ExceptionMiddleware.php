<?php

namespace AppKit\Http\Server\Middleware\Internal;

use AppKit\Http\Server\Middleware\HttpServerMiddlewareInterface;

use AppKit\Http\Message\HttpResponseException;
use AppKit\Http\Message\HttpError;

use Throwable;

class ExceptionMiddleware implements HttpServerMiddlewareInterface {
    private $log;

    function __construct($log) {
        $this -> log = $log;
    }

    public function processRequest($request, $next) {
        try {
            return $next($request);
        } catch(HttpResponseException $e) {
            throw $e;
        } catch(Throwable $e) {
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

            $this -> log -> error(
                "$remoteAddr $method $path - Uncaught exception while handling request",
                $e
            );

            throw new HttpError(500);
        }
    }
}
