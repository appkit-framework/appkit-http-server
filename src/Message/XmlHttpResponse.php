<?php

namespace AppKit\Http\Server\Message;

class XmlHttpResponse extends ServerHttpResponse {
    function __construct(
        $bodyText,
        $status = 200,
        $headers = []
    ) {
        parent::__construct(
            $status,
            [ 'Content-Type' => 'application/xml' ] + $headers,
            $bodyText
        );
    }
}
