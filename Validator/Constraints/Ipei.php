<?php

namespace TelNowEdge\FreePBX\Base\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Ipei extends Constraint
{
    public $message = 'The value is not a valid Ipei.';
}
