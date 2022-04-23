<?php

namespace Simple\Http\Middleware;

use Simple\Http\Request;

interface IMiddleware
{
    /**
     * @param Request $request
     */
    public function handle(Request $request): void;

}