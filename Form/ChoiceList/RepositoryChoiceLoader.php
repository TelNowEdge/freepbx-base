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

namespace TelNowEdge\FreePBX\Base\Form\ChoiceList;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\ChoiceList\ArrayChoiceList;
use Symfony\Component\Form\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface;

use function call_user_func;
use function is_object;

class RepositoryChoiceLoader implements ChoiceLoaderInterface
{
    private ?ArrayChoiceList $choiceList = null;

    public function __construct(private readonly ArrayCollection $collection) {}

    public function loadChoicesForValues(array $values, ?callable $value = null): array
    {
        if ([] === $values) {
            return [];
        }
        return $this->loadChoiceList($value)->getChoicesForValues($values);
    }

    public function loadChoiceList(?callable $value = null): ChoiceListInterface
    {
        if ($this->choiceList instanceof ArrayChoiceList) {
            return $this->choiceList;
        }

        return $this->choiceList = new ArrayChoiceList($this->collection, $value);
    }

    public function loadValuesForChoices(array $choices, ?callable $value = null): array
    {

        if ([] === $choices) {
            return [];
        }

        $values = [];

        foreach ($choices as $i => $givenChoice) {
            if (false === is_object($givenChoice)) {
                continue;
            }

            if (null !== $value) {
                $givenChoice = call_user_func($value, $givenChoice);
            }

            foreach ($this->collection as $val => $choice) {
                if (null !== $value) {
                    $val = call_user_func($value, $choice);

                    if ($val !== $givenChoice) {
                        continue;
                    }
                }

                if (null === $value && $choice->getId() !== $givenChoice->getId()) {
                    continue;
                }

                $values[$i] = (string) $val;
            }
        }

        return $values;
    }
}
