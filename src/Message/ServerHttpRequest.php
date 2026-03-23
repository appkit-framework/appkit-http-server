<?php

namespace AppKit\Http\Server\Message;

use AppKit\Http\Message\AbstractHttpRequest;

class ServerHttpRequest extends AbstractHttpRequest {
    private $remoteAddr;
    private $originalUrl;
    private $originalPath;

    function __construct(
        $method,
        $url,
        $headers,
        $body,
        $remoteAddr
    ) {
        parent::__construct($method, $url, $headers, $body);
        $this -> remoteAddr = $remoteAddr;
        $this -> originalUrl = $url;
        $this -> originalPath = $this -> getPath();
    }

    public function getRemoteAddr() {
        return $this -> remoteAddr;
    }

    // Url

    public function getOriginalUrl() {
        return $this -> originalUrl;
    }

    public function getOriginalPath() {
        return $this -> originalPath;
    }

    public function rewritePath($path) {
        $this -> setPath($path);
    }
}
