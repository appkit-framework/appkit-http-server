<?php

namespace AppKit\Http\Server\Middleware\Internal;

use AppKit\Http\Middleware\HttpMiddlewareInterface;

class TrailingSlashMiddleware implements HttpMiddlewareInterface {
    public function processRequest($request, $next) {
        $uri = $request -> getUri();
        $path = $uri -> getPath();
        if($path != '/' && str_ends_with($path, '/'))
            $request = $request -> withUri($uri -> withPath(rtrim($path, '/')));

        return $next($request);
    }
}
