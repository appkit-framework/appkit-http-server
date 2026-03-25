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

        $this -> setRemoteAddr($remoteAddr);
        $this -> storeOriginalUrl();
    }

    // Remote address

    public function getRemoteAddr() {
        return $this -> remoteAddr;
    }

    private function setRemoteAddr($remoteAddr) {
        $this -> remoteAddr = $remoteAddr;
    }

    // URL

    public function getOriginalUrl() {
        return $this -> originalUrl;
    }

    public function getOriginalPath() {
        return $this -> originalPath;
    }

    private function storeOriginalUrl() {
        $this -> originalUrl = $this -> getUrl();
        $this -> originalPath = $this -> getPath();
    }

    public function rewritePath($path) {
        $this -> setPath($path);
        return $this;
    }
}
