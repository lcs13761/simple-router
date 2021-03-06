<?php

class IpRestrictMiddleware extends \Simple\Http\Middleware\IpRestrictAccess
{

    protected $ipBlacklist = [
        '5.5.5.5',
        '8.8.*',
    ];

    protected $ipWhitelist = [
        '8.8.2.2',
    ];
}
