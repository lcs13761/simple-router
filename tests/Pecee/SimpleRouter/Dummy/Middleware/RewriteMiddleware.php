<?php

use Simple\Http\Middleware\IMiddleware;
use Simple\Http\Request;

class RewriteMiddleware implements IMiddleware
{

    public function handle(Request $request): void
    {

        $request->setRewriteCallback(function () {
            return 'ok';
        });
    }
}
