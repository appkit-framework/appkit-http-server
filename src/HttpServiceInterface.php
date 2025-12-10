<?php

namespace AppKit\Http\Server;

use AppKit\Http\Server\RouteProviderInterface;

interface HttpServiceInterface extends RouteProviderInterface {
    public function handleRequest($request, $handler, $vars, $extraParameters);
}
