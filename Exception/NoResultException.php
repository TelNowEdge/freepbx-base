<?php

namespace TelNowEdge\FreePBX\Base\Exception;

class NoResultException extends \Exception
{
    public function __construct($code = 0, $e = null)
    {
        parent::__construct('', $code, $e);
    }
}
