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

    public function get($route, $handler, $extraParameters = []) {
        $this -> addRoute('GET', $route, $handler, $extraParameters);
    }

    public function post($route, $handler, $extraParameters = []) {
        $this -> addRoute('POST', $route, $handler, $extraParameters);
    }

    public function put($route, $handler, $extraParameters = []) {
        $this -> addRoute('PUT', $route, $handler, $extraParameters);
    }

    public function delete($route, $handler, $extraParameters = []) {
        $this -> addRoute('DELETE', $route, $handler, $extraParameters);
    }

    public function patch($route, $handler, $extraParameters = []) {
        $this -> addRoute('PATCH', $route, $handler, $extraParameters);
    }

    public function head($route, $handler, $extraParameters = []) {
        $this -> addRoute('HEAD', $route, $handler, $extraParameters);
    }
}
