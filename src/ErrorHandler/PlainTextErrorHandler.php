<?php

namespace AppKit\Http\Server\ErrorHandler;

use AppKit\Http\Server\Message\ServerHttpResponse;

class PlainTextErrorHandler {
    public function handleError($error, $request) {
        return new ServerHttpResponse(
            $error -> getStatus(),
            [ 'Content-Type' => 'text/plain' ] + $error -> getHeaders(),
            $error -> getMessage() . "\n"
        );
    }
}
