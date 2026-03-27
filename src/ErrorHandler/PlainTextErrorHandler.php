<?php

namespace AppKit\Http\Server\ErrorHandler;

use AppKit\Http\Server\Message\PlainTextHttpResponse;

class PlainTextErrorHandler {
    public function handleError($error, $request) {
        return new PlainTextHttpResponse($error -> getMessage());
    }
}
