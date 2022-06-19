<?php

class ExceptionHandlerFirst implements \Simple\SimpleRouter\Handlers\IExceptionHandler
{
	public function handleError(\Simple\Http\Request $request, \Exception $error): void
	{
		global $stack;
		$stack[] = static::class;

		$request->setUrl(new \Simple\Http\Url('/'));
	}
}
