<?php

/*
 * Copyright 2019 TelNowEdge
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

namespace TelNowEdge\FreePBX\Base\Session;

use Symfony\Component\HttpFoundation\Session\SessionBagInterface;

class AutoExpireBag implements SessionBagInterface, \IteratorAggregate, \Countable
{
    protected $storageKey;
    private $name;
    private $autoExpires;

    public function __construct($storageKey = '_telnowedge_autoexpires')
    {
        $this->autoExpires = array();
        $this->name = 'autoExpires';
        $this->storageKey = $storageKey;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function set($name, $value, $timeout)
    {
        $expire = new \Datetime(sprintf('+%d seconds', $timeout));

        $this->autoExpires[$name] = array(
            'expire' => $expire->getTimestamp(),
            'value' => $value,
        );
    }

    public function get($name, $default = null)
    {
        $param = \array_key_exists($name, $this->autoExpires) ? $this->autoExpires[$name] : $default;

        $now = new \Datetime();
        $expire = new \Datetime();
        $expire->setTimestamp($param['expire']);

        $interval = $now->diff($expire);

        if (1 === $interval->invert) {
            $this->remove($name);

            return $default;
        }

        return $param['value'];
    }

    public function remove($name)
    {
        $retval = null;
        if (\array_key_exists($name, $this->autoExpires)) {
            $retval = $this->autoExpires[$name];
            unset($this->autoExpires[$name]);
        }

        return $retval;
    }

    public function has($name)
    {
        $param = \array_key_exists($name, $this->autoExpires);

        $now = new \Datetime();
        $expire = new \Datetime();
        $expire->setTimestamp($param['expire']);

        $interval = $now->diff($expire);

        if (1 === $interval->invert) {
            $this->remove($name);

            return false;
        }

        return true;
    }

    public function initialize(array &$autoExpires)
    {
        $this->autoExpires = &$autoExpires;
    }

    public function getStorageKey()
    {
        return $this->storageKey;
    }

    public function clear()
    {
        $return = $this->attributes;
        $this->attributes = array();

        return $return;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->autoExpires);
    }

    public function count()
    {
        return \count($this->autoExpires);
    }
}
