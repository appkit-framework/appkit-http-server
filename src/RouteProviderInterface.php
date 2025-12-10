<?php

namespace AppKit\Http\Server;

interface RouteProviderInterface {
    public function setupRoutes($routeCollector);
}
