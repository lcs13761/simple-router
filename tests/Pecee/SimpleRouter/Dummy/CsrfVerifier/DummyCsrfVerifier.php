<?php

class DummyCsrfVerifier extends \Simple\Http\Middleware\BaseCsrfVerifier
{

    protected $except = [
        '/exclude-page',
        '/exclude-all/*',
    ];

    protected $include = [
        '/exclude-all/include-page',
    ];

    public function testSkip(\Simple\Http\Request $request)
    {
        return $this->skip($request);
    }
}
