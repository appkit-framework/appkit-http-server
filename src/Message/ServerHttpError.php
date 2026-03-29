<?php

namespace AppKit\Http\Server\Message;

use AppKit\Http\Message\AbstractHttpError;

class ServerHttpError extends AbstractHttpError {
    function __construct(
        $status = 500,
        $message = null,
        $headers = [],
        $previous = null
    ) {
        parent::__construct(
            new ServerHttpResponse($status, $headers),
            $message,
            $previous
        );
    }
}
