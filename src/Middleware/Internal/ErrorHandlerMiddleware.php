<?php

namespace AppKit\Http\Server\Middleware\Internal;

use AppKit\Http\Middleware\HttpMiddlewareInterface;
use AppKit\Http\Message\HttpError;

class ErrorHandlerMiddleware implements HttpMiddlewareInterface {
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

            $setReason = $request -> getAttribute(AccessLogMiddleware::class)['setReason'];
            $setReason($e -> getMessage());

            $response = $this -> errorHandler -> handleError($e, $request)
                -> withStatus($e -> getCode());
            foreach($e -> getHeaders() as $name => $value)
                $response = $response -> withHeader($name, $value);

            return $response;
        }
    }

    public function setErrorHandler($errorHandler) {
        $this -> errorHandler = $errorHandler;

        return $this;
    }
}
