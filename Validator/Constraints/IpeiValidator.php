<?php

namespace TelNowEdge\FreePBX\Base\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class IpeiValidator extends ConstraintValidator
{
    public function validate($value, Constraint $consraint)
    {
        if (null === $value) {
            return true;
        }

        if (0 === preg_match('/^\d{12}[0-9\*]$/', $value)) {
            $this->context->addViolation($consraint->message);
        }

        if ($this->calculCrc($value) !== $value) {
            $this->context->addViolation($consraint->message);
        }

        return true;
    }

    private function calculCrc($ipei)
    {
        $ipei = substr($ipei, 0, 12);
        $crc = 0;

        for ($i = 0; $i < 12; $i++) {
            $crc += $ipei[$i] * ($i + 1);
        }

        $crc = $crc % 11;

        $crc = $crc === 10 ? '*' : $crc;

        return $ipei.$crc;
    }
}
