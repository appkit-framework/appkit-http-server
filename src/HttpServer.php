<?php

namespace AppKit\Http\Server;

use AppKit\StartStop\StartStopInterface;
use AppKit\Http\Server\RouteCollector;
use AppKit\Http\Exception\HttpError;
use AppKit\Http\Server\Exception\HttpServerException;
use AppKit\Health\HealthIndicatorInterface;
use AppKit\Health\HealthCheckResult;

use Throwable;
use React\Socket\SocketServer;
use React\Http\HttpServer as ReactHttpServer;
use FastRoute\Dispatcher;
use React\Http\Message\Response;
use function React\Async\async;
use function FastRoute\simpleDispatcher;

class HttpServer implements StartStopInterface, HealthIndicatorInterface {
    private $log;
    private $address;
    private $http;
    private $socket;
    private $services;
    private $dispatcher;

    function __construct($log, $address, $port) {
        $this -> log = $log -> withModule($this, $port);
        $this -> address = $address.':'.$port;
        $this -> services = [];
    }

    public function start() {
        $this -> dispatcher = simpleDispatcher(
            function($routeCollector) {
                return $this -> collectRoutes($routeCollector);
            },
            [
                'routeCollector' => RouteCollector::class
            ]
        );

        try {
            $this -> socket = new SocketServer($this -> address);
            $this -> http = new ReactHttpServer(async(function($request) {
                return $this -> handleRequest($request);
            }));
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

        $this -> log -> info(
            'Started HTTP server on '.
            $this -> address.
            ' with '.
            count($this -> services).
            ' services'
        );
    }

    public function stop() {
        $this -> socket -> close();
        $this -> http = null;
        $this -> socket = null;

        $this -> log -> info('Stopped HTTP server');
    }

    public function checkHealth() {
        $serviceGroup = [];
        foreach($this -> services as $serviceRecord) {
            if($serviceRecord['service'] instanceof HealthIndicatorInterface) {
                $key = get_class($serviceRecord['service']);
                if($serviceRecord['prefix'] !== '')
                    $key .= ' at '.$serviceRecord['prefix'];

                $serviceGroup[$key] = $serviceRecord['service'];
            }
        }

        return new HealthCheckResult([
            'Server listening' => ($this -> http !== null),
            'HTTP services' => $serviceGroup
        ]);
    }

    public function addService($service, $prefix = '') {
        $this -> services[] = [
            'service' => $service,
            'prefix' => $prefix
        ];

        $prefixLog = $prefix != '' ? " at $prefix" : '';
        $this -> log -> debug('Registered service '.get_class($service).$prefixLog);
    }

    private function collectRoutes($routeCollector) {
        foreach($this -> services as $service) {
            $routeCollector -> addService($service['service'], $service['prefix']);
            $this -> log -> debug('Collected routes from service '.get_class($service['service']));
        }
        $this -> log -> debug('Collected all routes');
    }

    private function handleRequest($request) {
        $method = $request -> getMethod();
        $path = $request -> getUri() -> getPath();

        $routeInfo = $this -> dispatcher -> dispatch($method, $path);

        try {
            switch($routeInfo[0]) {
                case Dispatcher::NOT_FOUND:
                    $this -> log -> debug("$method $path: Route not found");
                    throw new HttpError(404);

                case Dispatcher::METHOD_NOT_ALLOWED:
                    $this -> log -> debug("$method $path: Method not allowed");
                    throw new HttpError(405);

                case Dispatcher::FOUND:
                    $serviceHandler = [
                        $routeInfo[1]['extraParameters']['_service'],
                        'handleRequest'
                    ];
                    $serviceName = get_class($serviceHandler[0]);
                    $this -> log -> debug("$method $path: Request dispatched to $serviceName");
                    try {
                        return $serviceHandler(
                            $request,
                            $routeInfo[1]['handler'],
                            $routeInfo[2],
                            $routeInfo[1]['extraParameters']
                        );
                    } catch(HttpError $e) {
                        throw $e;
                    } catch(Throwable $e) {
                        $this -> log -> error(
                            "$method $path: Uncaught exception while handling the request by $serviceName",
                            $e
                        );
                        throw new HttpError(500);
                    }
            }
        } catch(HttpError $e) {
            return new Response(
                $e -> getCode(),
                [ 'Content-Type' => 'text/plain' ],
                $e -> getMessage() . "\n"
            );
        }
    }
}
