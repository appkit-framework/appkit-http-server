<?php

namespace AppKit\Http\Server\ErrorHandler;

use AppKit\Http\Server\Message\PlainTextHttpResponse;

class PlainTextErrorHandler implements HttpErrorHandlerInterface {
    public function handleError($error, $request) {
        return new PlainTextHttpResponse(
            $error -> getMessage(),
            $error -> getResponse() -> getHeaders()
        );
    }
}
