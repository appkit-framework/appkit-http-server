<?php

namespace AppKit\Http\Server\Message;

use AppKit\Http\Message\AbstractHttpRequest;

class ServerHttpRequest extends AbstractHttpRequest {
    private $remoteAddr;
    private $originalTarget;
    private $originalPath;

    function __construct(
        $method,
        $target,
        $headers,
        $body,
        $remoteAddr
    ) {
        parent::__construct($method, $target, $headers, $body);

        $this -> setRemoteAddr($remoteAddr);
        $this -> storeOriginalTarget();
    }

    // Remote address

    public function getRemoteAddr() {
        return $this -> remoteAddr;
    }

    private function setRemoteAddr($remoteAddr) {
        $this -> remoteAddr = $remoteAddr;
    }

    // Target

    public function getOriginalTarget() {
        return $this -> originalTarget;
    }

    public function getOriginalPath() {
        return $this -> originalPath;
    }

    private function storeOriginalTarget() {
        $this -> originalTarget = $this -> getTarget();
        $this -> originalPath = $this -> getPath();
    }

    public function rewritePath($path) {
        $this -> setPath($path);
        return $this;
    }
}
