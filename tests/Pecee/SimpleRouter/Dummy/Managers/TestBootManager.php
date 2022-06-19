<?php

class TestBootManager implements \Simple\SimpleRouter\IRouterBootManager
{

    protected $rewrite;

    public function __construct(array $rewrite)
    {
        $this->rewrite = $rewrite;
    }

    /**
     * Called when router loads it's routes
     *
     * @param \Simple\SimpleRouter\Router $router
     * @param \Simple\Http\Request $request
     */
    public function boot(\Simple\SimpleRouter\Router $router, \Simple\Http\Request $request): void
    {
        foreach ($this->rewrite as $url => $rewrite) {
            // If the current url matches the rewrite url, we use our custom route

            if ($request->getUrl()->contains($url) === true) {
                $request->setRewriteUrl($rewrite);
            }
        }
    }
}
