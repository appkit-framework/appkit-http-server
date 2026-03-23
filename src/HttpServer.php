<?php

namespace AppKit\Http\Server;

use AppKit\Http\Server\HttpServerException;
use AppKit\Http\Server\Middleware\Internal\RequestIdMiddleware;
use AppKit\Http\Server\Middleware\Internal\AccessLogMiddleware;
use AppKit\Http\Server\Middleware\Internal\ServerHeadersMiddleware;
use AppKit\Http\Server\Middleware\Internal\RedirectMiddleware;
use AppKit\Http\Server\Middleware\Internal\ErrorHandlerMiddleware;
use AppKit\Http\Server\Middleware\Internal\ExceptionMiddleware;
use AppKit\Http\Server\ErrorHandler\PlainTextErrorHandler;
use AppKit\Http\Server\Message\ServerHttpRequest;

use AppKit\StartStop\StartStopInterface;
use AppKit\Health\HealthIndicatorInterface;
use AppKit\Health\HealthCheckResult;
use AppKit\Http\Middleware\HttpMiddlewarePipeline;
use function AppKit\Async\async;

use Throwable;
use React\Socket\SocketServer;
use React\Http\HttpServer as ReactHttpServer;
use React\Http\Message\Response;

class HttpServer implements StartStopInterface, HealthIndicatorInterface {
    private $address;
    private $port;
    private $resource;

    private $log;
    private $pipeline;
    private $socket;
    private $http;

    function __construct(
        $log,
        $resource,
        $address = '127.0.0.1',
        $port = 8000
    ) {
        $this -> address = $address;
        $this -> port = $port;
        $this -> resource = $resource;

        $this -> log = $log -> withModule(static::class, $port);

        $this -> pipeline = new HttpMiddlewarePipeline([$this -> resource, 'dispatchRequest']);
        $this -> pipeline
            -> addMiddleware(
                new RequestIdMiddleware($this -> log)
            ) -> addMiddleware(
                new AccessLogMiddleware($this -> log)
            ) -> addMiddleware(
                new ServerHeadersMiddleware()
            ) -> addMiddleware(
                new RedirectMiddleware()
            ) -> addMiddleware(
                new ErrorHandlerMiddleware(
                    new PlainTextErrorHandler()
                )
            ) -> addMiddleware(
                new ExceptionMiddleware($this -> log)
            );
    }

    public function start() {
        try {
            $this -> socket = new SocketServer($this -> address . ':' . $this -> port);
            $this -> http = new ReactHttpServer(async(function($psrRequest) {
                return $this -> handlePsrRequest($psrRequest);
            }));
            $this -> http -> listen($this -> socket);
        } catch(Throwable $e) {
            $this -> http = null;
            $this -> socket = null;

            $error = 'Failed to create server socket';
            $this -> log -> error(
                $error,
                [ 'address' => $this -> address, 'port' => $this -> port ],
                $e
            );
            throw new HttpServerException(
                $error,
                previous: $e
            );
        }

        $this -> log -> info(
            'Listening on {address}:{port}',
            [ 'address' => $this -> address, 'port' => $this -> port ]
        );
    }

    public function stop() {
        $this -> socket -> close();
        $this -> http = null;
        $this -> socket = null;

        $this -> log -> info('Closed server socket');
    }

    public function checkHealth() {
        $data = [
            'Server listening' => ($this -> http !== null)
        ];

        if($this -> resource instanceof HealthIndicatorInterface)
            $data['Resource ' . get_class($this -> resource)] = $this -> resource;

        return new HealthCheckResult($data);
    }

    private function handlePsrRequest($psrRequest) {
        $request = new ServerHttpRequest(
            $psrRequest -> getMethod(),
            $psrRequest -> getRequestTarget(),
            $psrRequest -> getHeaders(),
            $psrRequest -> getBody(),
            $psrRequest -> getServerParams()['REMOTE_ADDR']
        );

        $response = $this -> pipeline -> processRequest($request);

        return new Response(
            $response -> getStatus(),
            $response -> getHeaders(),
            $response -> getBody()
        );
    }
}
