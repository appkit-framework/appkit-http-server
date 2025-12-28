<?php

namespace AppKit\Http\Server\ErrorHandler;

use AppKit\Http\Message\HttpResponse;

class PlainTextErrorHandler {
    public function handleError($error, $request) {
        return new HttpResponse(
            headers: [ 'Content-Type' => 'text/plain' ],
            body: $error -> getMessage() . "\n"
        );
    }
}
