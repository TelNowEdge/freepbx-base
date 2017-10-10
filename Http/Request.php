<?php
namespace TelNowEdge\FreePBX\Base\Http;

use Symfony\Component\HttpFoundation\Request as BaseRequest;
use Symfony\Component\HttpFoundation\Session\Session;

class Request
{
    public static function create()
    {
        return BaseRequest::createFromGlobals();
    }
}
