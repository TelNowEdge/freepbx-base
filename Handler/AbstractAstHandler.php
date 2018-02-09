<?php

namespace TelNowEdge\FreePBX\Base\Handler;

abstract class AbstractAstHandler
{
    /**
     * class AGI_AsteriskManager (libraries/php-asmanager.php).
     */
    protected $connection;

    protected $eventDispatcher;

    public function setConnection(\AGI_AsteriskManager $connection)
    {
        $this->connection = $connection;
    }

    public function setEventDispatcher(\Symfony\Component\EventDispatcher\EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }
}
