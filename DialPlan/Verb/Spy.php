<?php

namespace TelNowEdge\FreePBX\Base\DialPlan\Verb;

class Spy implements VerbInterface
{
    private $prefix;
    private $options;

    public function __construct($prefix = '', $options = '')
    {
        $this->prefix = $prefix;
        $this->options = $options;
    }

    public function output()
    {
        return sprintf(
            'ExtenSpy(%s%s)',
            $this->prefix,
            $this->options ? ','.$this->options : ''
        );
    }
}
