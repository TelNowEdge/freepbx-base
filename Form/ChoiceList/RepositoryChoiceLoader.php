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
use Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface;

class RepositoryChoiceLoader implements ChoiceLoaderInterface
{
    private $collection;

    public function __construct(ArrayCollection $collection)
    {
        $this->collection = $collection;
    }

    public function loadChoiceList($value = null)
    {
        if (null !== $this->choiceList) {
            return $this->choiceList;
        }

        return $this->choiceList = new ArrayChoiceList($this->collection, $value);
    }

    public function loadChoicesForValues(array $values, $value = null)
    {
        if (true === empty($values)) {
            return array();
        }

        return $this->loadChoiceList($value)->getChoicesForValues($values);
    }

    public function loadValuesForChoices(array $choices, $value = null)
    {
        if (true === empty($choices)) {
            return array();
        }

        $values = array();

        foreach ($choices as $i => $givenChoice) {
            if (false === is_object($givenChoice)) {
                continue;
            }

            foreach ($this->collection as $value => $choice) {
                if ($choice->getId() !== $givenChoice->getId()) {
                    continue;
                }

                $values[$i] = $value;
            }
        }

        return $values;
    }
}
