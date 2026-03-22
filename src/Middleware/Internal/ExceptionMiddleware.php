<?php

namespace AppKit\Http\Server\Middleware\Internal;

use AppKit\Http\Server\Middleware\HttpServerMiddlewareInterface;

use AppKit\Http\Message\AbstractHttpResponseException;
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
        } catch(AbstractHttpResponseException $e) {
            throw $e;
        } catch(Throwable $e) {
            $this -> log -> error(
                'Uncaught exception while handling request',
                $e
            );

            throw new HttpError(500);
        }
    }
}
