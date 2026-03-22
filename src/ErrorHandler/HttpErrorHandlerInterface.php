<?php

namespace AppKit\Http\Server\ErrorHandler;

interface HttpErrorHandlerInterface {
    public function handleError($error, $request);
}
