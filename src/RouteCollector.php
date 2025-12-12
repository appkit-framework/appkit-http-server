<?php

namespace AppKit\Http\Server;

use FastRoute\RouteCollector as FastRouteRouteCollector;

class RouteCollector extends FastRouteRouteCollector {
    private $currentService;

    public function addRoute($httpMethod, $route, $handler, $extraParameters = []) {
        // TODO: Stop injecting extraParameters into the handler once a new FastRoute starts supporting them
        $extraParameters['_service'] = $this -> currentService;
        parent::addRoute(
            $httpMethod,
            $route,
            [
                'handler' => $handler,
                'extraParameters' => $extraParameters
            ]
        );
    }

    public function addService($service, $prefix) {
        $this -> currentService = $service;
        $this -> addGroup($prefix, [$service, 'setupRoutes']);
    }
}
