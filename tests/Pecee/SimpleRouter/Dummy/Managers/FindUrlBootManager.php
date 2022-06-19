<?php

class FindUrlBootManager implements \Simple\SimpleRouter\IRouterBootManager
{
    protected $result;

    public function __construct(&$result)
    {
        $this->result = &$result;
    }

    /**
     * Called when router loads it's routes
     *
     * @param \Simple\SimpleRouter\Router $router
     * @param \Simple\Http\Request $request
     */
    public function boot(\Simple\SimpleRouter\Router $router, \Simple\Http\Request $request): void
    {
        $contact = $router->findRoute('contact');

        if ($contact !== null) {
            $this->result = true;
        }
    }
}
