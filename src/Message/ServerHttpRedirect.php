<?php

namespace AppKit\Http\Server\Message;

use AppKit\Http\Message\AbstractHttpRedirect;

class ServerHttpRedirect extends AbstractHttpRedirect {
    function __construct(
        $location,
        $status = 302,
        $previous = null
    ) {
        parent::__construct(
            new ServerHttpResponse(
                $status,
                [ 'Location' => $location ]
            ),
            $previous
        );
    }
}
