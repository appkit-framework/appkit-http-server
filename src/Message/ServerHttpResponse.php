<?php

namespace AppKit\Http\Server\Message;

use AppKit\Http\Message\AbstractHttpResponse;

class ServerHttpResponse extends AbstractHttpResponse {
    function __construct(
        $status = 200,
        $headers = [],
        $bodyText = ''
    ) {
        $this -> setStatus($status)
            -> setHeaders($headers)
            -> setBodyText($bodyText);
    }

    // Status

    public function setStatus($status) {
        return parent::setStatus($status);
    }

    // Headers

    public function setHeader($name, $value) {
        return parent::setHeader($name, $value);
    }

    public function addHeader($name, $value) {
        return parent::addHeader($name, $value);
    }

    public function unsetHeader($name) {
        return parent::unsetHeader($name);
    }

    // Body

    public function setBodyText($bodyText) {
        return parent::setBodyText($bodyText);
    }
}
