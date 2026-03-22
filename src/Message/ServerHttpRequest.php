<?php

namespace AppKit\Http\Server\Message;

use AppKit\Http\Message\AbstractHttpRequest;

class ServerHttpRequest extends AbstractHttpRequest {
    private $remoteAddr;

    function __construct(
        $method,
        $url,
        $headers,
        $body,
        $remoteAddr
    ) {
        parent::__construct($method, $url, $headers, $body);
        $this -> remoteAddr = $remoteAddr;
    }

    public function getRemoteAddr() {
        return $this -> remoteAddr;
    }
}
