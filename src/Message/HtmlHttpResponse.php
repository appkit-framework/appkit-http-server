<?php

namespace AppKit\Http\Server\Message;

class HtmlHttpResponse extends ServerHttpResponse {
    function __construct(
        $bodyText,
        $status = 200,
        $headers = []
    ) {
        parent::__construct(
            $status,
            [ 'Content-Type' => 'text/html; charset=utf-8' ] + $headers,
            $bodyText
        );
    }
}
