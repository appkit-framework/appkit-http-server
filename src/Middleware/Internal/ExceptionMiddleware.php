<?php

namespace AppKit\Http\Server\Middleware\Internal;

use AppKit\Http\Server\Middleware\ServerHttpMiddlewareInterface;

use AppKit\Http\Server\Message\ServerHttpError;
use AppKit\Http\Server\Message\ServerHttpRedirect;

use Throwable;

class ExceptionMiddleware implements ServerHttpMiddlewareInterface {
    private $log;

    function __construct($log) {
        $this -> log = $log;
    }

    public function processRequest($request, $next) {
        try {
            return $next($request);
        } catch(ServerHttpError | ServerHttpRedirect $e) {
            throw $e;
        } catch(Throwable $e) {
            $this -> log -> error(
                'Uncaught exception while handling request',
                $e
            );
            throw new ServerHttpError(500);
        }
    }
}
