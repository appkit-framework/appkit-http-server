<?php

namespace AppKit\Http\Server\Resource;

use AppKit\Http\Server\Middleware\Internal\ErrorHandlerMiddleware;
use AppKit\Http\Server\Middleware\Internal\ExceptionMiddleware;

use AppKit\Health\HealthIndicatorInterface;
use AppKit\Health\HealthCheckResult;
use AppKit\Http\Middleware\HttpMiddlewarePipeline;

abstract class AbstractHttpResource implements HealthIndicatorInterface {
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

    public function checkHealth() {
        $data = $this -> getAdditionalHealthData();

        foreach($this -> pipeline -> getMiddlewares() as $middleware)
            if($middleware instanceof HealthIndicatorInterface)
                $data['Middlewares'][get_class($middleware)] = $middleware;

        return new HealthCheckResult($data);
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

    protected function getAdditionalHealthData() {
        return [];
    }
}
