<?php

class ExceptionHandlerThird implements \Simple\SimpleRouter\Handlers\IExceptionHandler
{
	public function handleError(\Simple\Http\Request $request, \Exception $error): void
	{
		global $stack;
		$stack[] = static::class;

		throw new ResponseException('ExceptionHandler loaded');
	}
}
