<?php

class ExceptionHandler implements \Simple\SimpleRouter\Handlers\IExceptionHandler
{
	public function handleError(\Simple\Http\Request $request, \Exception $error): void
	{
		echo $error->getMessage();
	}
}
