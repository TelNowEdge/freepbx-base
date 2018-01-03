<?php

namespace TelNowEdge\FreePBX\Base\Validator\Constraints;

use Symfony\Component\Validator\Constraints\Choice as BaseChoice;

/**
 * @Annotation
 */
class Choice extends BaseChoice
{
    public $service;

    public $nullable;
}
