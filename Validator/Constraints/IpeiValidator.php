<?php

/*
 * Copyright 2016 TelNowEdge
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace TelNowEdge\FreePBX\Base\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class IpeiValidator extends ConstraintValidator
{
    public function validate($value, Constraint $consraint): true
    {
        if (null === $value) {
            return true;
        }

        if ((0 === preg_match('/^\d{12}[0-9\*]$/', $value)) && (0 === preg_match('/^0[0-9a-fA-F]{9}$/', $value))) {
            $this->context->addViolation($consraint->message);
        }

        if (1 === preg_match('/^\d{12}[0-9\*]$/', $value)) {
            // C'est un ipei
            if ($this->calculCrc($value) !== $value) {
                $this->context->addViolation($consraint->message);
            }
        }

        return true;
    }

    private function calculCrc(string $ipei): string
    {
        $ipei = substr($ipei, 0, 12);
        $crc = 0;

        for ($i = 0; $i < 12; ++$i) {
            $crc += $ipei[$i] * ($i + 1);
        }

        $crc = $crc % 11;

        $crc = 10 === $crc ? '*' : $crc;

        return $ipei . $crc;
    }
}
