<?php

namespace TelNowEdge\FreePBX\Base\DialPlan\Verb;

class ExtVerbose implements VerbInterface
{
    private $level;
    private $message;

    public function __construct($level = 1, $message = '')
    {
        $this->level = $level;
        $this->message = $message;
    }

    public function output()
    {
        return sprintf(
            'Verbose(%d, %s)',
            $this->level,
            $this->message
        );
    }
}
