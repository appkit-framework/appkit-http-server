<?php

namespace AppKit\Http\Server\Resource;

use AppKit\Http\Middleware\HttpMiddlewarePipeline;
use AppKit\Http\Server\Middleware\Internal\ErrorHandlerMiddleware;
use AppKit\Http\Server\Middleware\Internal\ExceptionMiddleware;

// TODO How to handle middlewares with health interface

abstract class AbstractHttpResource {
    protected $log;

    protected $pipeline;
    protected $errorHandlerMw;

    abstract protected function handleRequest($request);

    function __construct($log) {
        $this -> log = $log;

        $this -> pipeline = new HttpMiddlewarePipeline(function($request) {
            return $this -> handleRequest($request);
        });
        $this -> errorHandlerMw = new ErrorHandlerMiddleware();
        $this -> pipeline
            -> addMiddleware(
                $this -> errorHandlerMw
            ) -> addMiddleware(
                new ExceptionMiddleware($this -> log)
            );
    }

    public function dispatchRequest($request) {
        return $this -> pipeline -> processRequest($request);
    }

    public function addMiddleware($middleware) {
        $this -> pipeline -> addMiddleware($middleware);
        $this -> log -> debug(
            'Registered middleware {middleware}',
            [ 'middleware' => get_class($middleware) ]
        );

        return $this;
    }

    public function setErrorHandler($errorHandler) {
        $this -> errorHandlerMw -> setErrorHandler($errorHandler);
        $this -> log -> debug(
            'Set error handler {errorHandler}',
            [ 'errorHandler' => get_class($errorHandler) ]
        );

        return $this;
    }
}
