<?php

namespace AppKit\Http\Server\Message;

class PlainTextHttpResponse extends ServerHttpResponse {
    function __construct(
        $bodyText,
        $status = 200,
        $headers = []
    ) {
        parent::__construct(
            $status,
            [ 'Content-Type' => 'text/plain; charset=utf-8' ] + $headers,
            $bodyText
        );
    }
}
