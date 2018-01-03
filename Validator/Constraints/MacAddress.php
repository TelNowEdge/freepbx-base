<?php

namespace TelNowEdge\FreePBX\Base\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class MacAddress extends Constraint
{
    public $message = 'The value is not a valid mac address.';

    public $allowAuto = false;

    public $changeAll = false;
}
