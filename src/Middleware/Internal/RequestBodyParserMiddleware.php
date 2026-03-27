<?php

namespace AppKit\Http\Server\Middleware\Internal;

use AppKit\Http\Server\Middleware\ServerHttpMiddlewareInterface;
use AppKit\Http\Server\Response\ServerHttpError;

use AppKit\Json\Json;

use Throwable;

class RequestBodyParserMiddleware implements ServerHttpMiddlewareInterface {
    public function processRequest($request, $next) {
        [ $type ] = explode(';', strtolower($request -> getHeaderLine('Content-Type')));

        if($type == 'application/json') {
            try {
                $request -> setParsedBody(Json::decode($request -> getBodyText()));
            } catch(Throwable $e) {}
        }

        return $next($request);
    }
}
