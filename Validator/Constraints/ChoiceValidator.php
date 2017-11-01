<?php

namespace TelNowEdge\FreePBX\Base\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\ChoiceValidator as BaseChoiceValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ChoiceValidator extends BaseChoiceValidator
{
    private $container;

    public function __construct($container)
    {
        xdebug_break();
        $this->container = $container;
    }

    public function validate($value, Constraint $consraint)
    {
        if ($consraint->service) {
            xdebug_break();
        }

        parent::validate($value, $consraint);
    }
}
