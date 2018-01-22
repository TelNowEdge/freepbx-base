<?php

namespace TelNowEdge\FreePBX\Base\DialPlan\Verb;

class Raw implements VerbInterface
{
    private $data;

    public function __construct($data = '')
    {
        $this->data = $data;
    }

    public function output()
    {
        return sprintf(
               '%s',
               $this->data
               );
    }
}
