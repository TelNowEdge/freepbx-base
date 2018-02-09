<?php

namespace TelNowEdge\FreePBX\Base\Manager;

class AsteriskManagerFactory
{
    public function getAsteriskManager()
    {
        global $astman;

        return $astman;
    }
}
