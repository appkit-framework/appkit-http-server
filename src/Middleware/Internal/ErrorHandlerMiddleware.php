<?php

namespace AppKit\Http\Server\Middleware\Internal;

use AppKit\Http\Server\Middleware\HttpServerMiddlewareInterface;

use AppKit\Http\Message\HttpError;
use AppKit\Http\Message\AbstractHttpResponseException;

class ErrorHandlerMiddleware implements HttpServerMiddlewareInterface {
    private $errorHandler;

    function __construct($errorHandler = null) {
        $this -> errorHandler = $errorHandler;
    }

    public function processRequest($request, $next) {
        try {
            return $next($request);
        } catch(HttpError $e) {
            if(!$this -> errorHandler)
                throw $e;

            $request -> setAttribute(AbstractHttpResponseException::class, $e);

            return $this -> errorHandler -> handleError($e, $request);
        }
    }

    public function setErrorHandler($errorHandler) {
        $this -> errorHandler = $errorHandler;
    }
}
