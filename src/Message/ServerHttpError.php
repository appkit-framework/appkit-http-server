<?php

namespace AppKit\Http\Server\Message;

use AppKit\Http\Message\AbstractHttpError;

class ServerHttpError extends AbstractHttpError {
    function __construct(
        $status = 500,
        $message = null,
        $previous = null
    ) {
        parent::__construct(
            new ServerHttpResponse($status),
            $message,
            $previous
        );
    }
}
