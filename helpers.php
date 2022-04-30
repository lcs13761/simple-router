<?php

use Simple\SimpleRouter\SimpleRouter as Router;
use Simple\Http\Url;
use Simple\Http\Response;
use Simple\Http\Request;
use Simple\Session\Session;

/**
 * Get url for a route by using either name/alias, class or method name.
 *
 * The name parameter supports the following values:
 * - Route name
 * - Controller/resource name (with or without method)
 * - Controller class name
 *
 * When searching for controller/resource by name, you can use this syntax "route.name@method".
 * You can also use the same syntax when searching for a specific controller-class "MyController@home".
 * If no arguments is specified, it will return the url for the current loaded route.
 *
 * @param string|null $name
 * @param string|array|null $parameters
 * @param array|null $getParams
 * @return \Simple\Http\Url
 * @throws \InvalidArgumentException
 */
function url(?string $name = null, $parameters = null, ?array $getParams = null): Url
{
    return Router::getUrl($name, $parameters, $getParams);
}

/**
 * @return \Simple\Http\Response
 */
function response(): Response
{
    return Router::response();
}

/**
 * @return \Simple\Http\Request
 */
function request(): Request
{
    return Router::request();
}

/**
 * @param string $url
 * @param int|null $code
 */
function redirect(string $url, ?int $code = null): void
{
    if ($code !== null) {
        response()->httpCode($code);
    }

    response()->redirect($url);
}

if (!function_exists('csrf_field')) {

    function csrf_field()
    {
        return '<input type="hidden" name="_token" value="' . csrf_token() . '" />';
    }
}


if (!function_exists('csrf_token')) {
    function csrf_token()
    {
       $baseVerifier = Router::router()->getCsrfVerifier();
        if ($baseVerifier !== null) {
            return $baseVerifier->getTokenProvider()->getToken();
        }

        throw new Exception('Application session store not set.');
    }
}


if (!function_exists('session')) {

    function session()
    {
        return new Session();
    }
}
