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

namespace TelNowEdge\FreePBX\Base\Model;

use Doctrine\Common\Collections\ArrayCollection;

class AbstractUncontrolledModel
{
    private ArrayCollection $storage;

    public function __construct()
    {
        $this->storage = new ArrayCollection();

        /*
         * Used by MacProvision
         */
        $this->storage->set('keys', new ArrayCollection());
    }

    public function __call($name, $args)
    {
        $value = reset($args);

        if (1 === preg_match('/^set(.*)$/', $name, $match)) {
            $prop = lcfirst($match[1]);

            if (1 === preg_match('/^key(\d{3})$/', $prop, $subMatch)) {
                $this->storage->get('keys')->set($subMatch[1], $value);

                return null;
            }

            $this->storage->set(lcfirst($prop), $value);
        }

        if (1 === preg_match('/^get(.*)$/', $name, $match)) {
            return $this->storage->get(lcfirst($match[1]));
        }
    }

    public function __clone()
    {
        $this->storage = clone $this->storage;
    }

    public function getAsObject(): ArrayCollection
    {
        return $this->storage;
    }

    public function getAll()
    {
        return $this->storage->toArray();
    }

    public function getKeys()
    {
        return $this->storage->get('keys');
    }

    public function setKeys(ArrayCollection $keys): void
    {
        $this->storage->set('keys', $keys);
    }
}
