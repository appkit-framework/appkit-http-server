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
        $bodyText,
        $body,
        $remoteAddr
    ) {
        $this -> setMethod($method)
            -> setTarget($target)
            -> storeOriginalTarget()
            -> setHeaders($headers)
            -> setBodyText($bodyText)
            -> setBody($body)
            -> setRemoteAddr($remoteAddr);
    }

    // Remote address

    public function getRemoteAddr() {
        return $this -> remoteAddr;
    }

    private function setRemoteAddr($remoteAddr) {
        $this -> remoteAddr = $remoteAddr;
        return $this;
    }

    // Target

    public function getOriginalTarget() {
        return $this -> originalTarget;
    }

    public function getOriginalPath() {
        return $this -> originalPath;
    }

    public function rewritePath($path) {
        return $this -> setPath($path);
    }

    private function storeOriginalTarget() {
        $this -> originalTarget = $this -> getTarget();
        $this -> originalPath = $this -> getPath();
        return $this;
    }

    // Body

    public function setParsedBody($body) {
        return $this -> setBody($body);
    }
}
