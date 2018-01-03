<?php

namespace TelNowEdge\FreePBX\Base\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class MacAddressValidator extends ConstraintValidator
{
    public function validate($value, Constraint $consraint)
    {
        if (true === $consraint->allowAuto && 'auto' === strtolower($value)) {
            return true;
        }

        if (1 === preg_match('/^(?:all\:)?(?:[0-9A-Fa-f]{12})|(?:(?:[0-9A-Fa-f]{2}(?::|-)){5}[0-9A-Fa-f]{2})|(?:[0-9A-Fa-f]{6}-[0-9A-Fa-f]{6})$/', $value)) {
            return true;
        }

        $this->context->addViolation($consraint->message);
    }
}
