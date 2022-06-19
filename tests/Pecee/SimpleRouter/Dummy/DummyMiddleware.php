<?php
require_once 'Exception/MiddlewareLoadedException.php';

use Simple\Http\Request;

class DummyMiddleware implements \Simple\Http\Middleware\IMiddleware
{
	public function handle(Request $request): void
	{
		throw new MiddlewareLoadedException('Middleware loaded!');
	}
}
