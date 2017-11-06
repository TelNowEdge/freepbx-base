<?php

namespace TelNowEdge\FreePBX\Base\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Depends extends Constraint
{
    public $field;
    public $depends;
    public $service;
    public $message = 'The value you selected is not a valid choice.';

    public function getRequiredOptions()
    {
        return array(
            'field',
            'depends',
            'service',
        );
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
