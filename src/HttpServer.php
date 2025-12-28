<?php

namespace AppKit\Http\Server;

use AppKit\Http\Server\HttpServerException;
use AppKit\Http\Server\Middleware\Internal\AccessLogMiddleware;
use AppKit\Http\Server\Middleware\Internal\TrailingSlashMiddleware;
use AppKit\Http\Server\Middleware\Internal\ErrorHandlerMiddleware;
use AppKit\Http\Server\Middleware\Internal\RedirectMiddleware;
use AppKit\Http\Server\Middleware\Internal\ExceptionMiddleware;
use AppKit\Http\Server\ErrorHandler\PlainTextErrorHandler;

use AppKit\StartStop\StartStopInterface;
use AppKit\Health\HealthIndicatorInterface;
use AppKit\Health\HealthCheckResult;
use AppKit\Http\Middleware\HttpMiddlewarePipeline;

use Throwable;
use React\Socket\SocketServer;
use React\Http\HttpServer as ReactHttpServer;
use function React\Async\async;

class HttpServer implements StartStopInterface, HealthIndicatorInterface {
    private $resource;

    private $log;
    private $address;
    private $pipeline;
    private $socket;
    private $http;

    function __construct($log, $address, $port, $resource) {
        $this -> resource = $resource;

        $this -> log = $log -> withModule($this, $port);
        $this -> address = $address.':'.$port;

        $this -> pipeline = new HttpMiddlewarePipeline([$this -> resource, 'dispatchRequest']);
        $this -> pipeline
            -> addMiddleware(
                new AccessLogMiddleware($this -> log)
            ) -> addMiddleware(
                new TrailingSlashMiddleware()
            ) -> addMiddleware(
                new ErrorHandlerMiddleware(
                    new PlainTextErrorHandler()
                )
            ) -> addMiddleware(
                new RedirectMiddleware()
            ) -> addMiddleware(
                new ExceptionMiddleware($this -> log)
            );
    }

    public function start() {
        try {
            $this -> socket = new SocketServer($this -> address);
            $this -> http = new ReactHttpServer(async([$this -> pipeline, 'processRequest']));
            $this -> http -> listen($this -> socket);
        } catch(Throwable $e) {
            $this -> http = null;
            $this -> socket = null;

            $error = 'Failed to create server socket';
            $this -> log -> error($error, $e);
            throw new HttpServerException(
                $error,
                previous: $e
            );
        }

        $this -> log -> info('Listening on ' . $this -> address);
    }

    public function stop() {
        $this -> socket -> close();
        $this -> http = null;
        $this -> socket = null;

        $this -> log -> info('Socket closed');
    }

    public function checkHealth() {
        $resultData = [
            'Server listening' => ($this -> http !== null)
        ];

        if($this -> resource instanceof HealthIndicatorInterface)
            $resultData['Resource ' . get_class($this -> resource)] = $this -> resource;

        return new HealthCheckResult($resultData);
    }
}
