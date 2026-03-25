<?php

namespace AppKit\Http\Server\ErrorHandler;

use AppKit\Http\Server\Message\ServerHttpResponse;

class PlainTextErrorHandler {
    public function handleError($error, $request) {
        return $error -> getResponse()
            -> setHeader('Content-Type', 'text/plain')
            -> setBody($error -> getMessage() . "\n");
    }
}
