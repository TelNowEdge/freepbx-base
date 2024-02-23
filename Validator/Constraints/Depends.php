<?php

/*
 * Copyright [2016] [TelNowEdge]
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

#[\Attribute]
class Depends extends Constraint
{
    public string $field;
    public string $depends;
    public array $service;
    public string $message = 'The value you selected is not a valid choice.';

    public function __construct(
        string $field,
        string $depends,
        array $service,
        string $message = null,
        array $groups = null,
        mixed $payload = null,
        array $options = [],
    ) {
        $options = array_merge(['field' => $field], ['depends' => $depends], ['service' => $service], $options);

        parent::__construct($options, $groups, $payload);
    }

    public function getDefaultOption(): array
    {
        return [
            'field',
            'depends',
            'service',
        ];
    }

    public function getRequiredOptions(): array
    {
        return [
            'field',
            'depends',
            'service',
        ];
    }

    public function getTargets(): array|string
    {
        return self::CLASS_CONSTRAINT;
    }
}
