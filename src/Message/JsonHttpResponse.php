<?php

namespace AppKit\Http\Server\Message;

use AppKit\Json\Json;

class JsonHttpResponse extends ServerHttpResponse {
    function __construct(
        $body,
        $status = 200,
        $headers = []
    ) {
        $this -> setStatus($status)
            -> setHeaders($headers)
            -> setHeader('Content-Type', 'application/json')
            -> setBody($body);
    }

    public function setBody($body) {
        parent::setBody($body);
        $this -> setBodyText(
            Json::encode($body)
        );
    }
}
