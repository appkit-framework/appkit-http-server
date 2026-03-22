<?php

namespace AppKit\Http\Server\Message;

use AppKit\Http\Message\AbstractHttpResponse;

class ServerHttpResponse extends AbstractHttpResponse {
    public function setStatus($status) {
        parent::setStatus($status);
        return $this;
    }

    public function setHeader($name, $value) {
        parent::setHeader($name, $value);
        return $this;
    }

    public function addHeader($name, $value) {
        parent::addHeader($name, $value);
        return $this;
    }

    public function unsetHeader($name) {
        parent::unsetHeader($name);
        return $this;
    }

    public function setBody($body) {
        parent::setBody($body);
        return $this;
    }
}
