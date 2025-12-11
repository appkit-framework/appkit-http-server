<?php

namespace AppKit\Http\Server;

use FastRoute\RouteCollector as FastRouteRouteCollector;

class RouteCollector extends FastRouteRouteCollector {
    private $currentService;
    private $currentServicePrefix = '';

    public function addRoute($httpMethod, $route, $handler, $extraParameters = []) {
        // TODO: Stop injecting extraParameters into the handler once a new FastRoute starts supporting them
        parent::addRoute(
            $httpMethod,
            $route,
            [
                'handler' => $handler,
                'extraParameters' => [
                    '_service' => $this -> currentService,
                    '_servicePrefix' => $this -> currentServicePrefix
                ] + $extraParameters
            ]
        );
    }

    public function addService($service, $prefix) {
        $this -> currentService = $service;
        $this -> currentServicePrefix = $prefix;
        $this -> addGroup($prefix, [$service, 'setupRoutes']);
    }
}
