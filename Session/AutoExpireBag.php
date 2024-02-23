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

use Countable;
use IteratorAggregate;
use Symfony\Component\HttpFoundation\Session\SessionBagInterface;
use Traversable;

use function count;

class AutoExpireBag implements SessionBagInterface, \IteratorAggregate, \Countable
{
    protected string $storageKey;
    private string $name = 'autoExpires';
    private array $autoExpires = [];

    private array $attributes;

    public function __construct(string $storageKey = '_telnowedge_autoexpires')
    {
        $this->storageKey = $storageKey;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param mixed $value
     * @param mixed $timeout
     *
     * @throws \Exception
     */
    public function set(string $name, $value, $timeout): void
    {
        $expire = new \Datetime(sprintf('+%d seconds', $timeout));

        $this->autoExpires[$name] = [
            'expire' => $expire->getTimestamp(),
            'value' => $value,
        ];
    }

    public function get(string $name, $default = null)
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

    public function remove(string $name)
    {
        $retval = null;
        if (\array_key_exists($name, $this->autoExpires)) {
            $retval = $this->autoExpires[$name];
            unset($this->autoExpires[$name]);
        }

        return $retval;
    }

    public function has(string $name): bool
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

    public function initialize(array &$autoExpires): void
    {
        $this->autoExpires = &$autoExpires;
    }

    public function getStorageKey(): string
    {
        return $this->storageKey;
    }

    public function clear(): null
    {
        $return = $this->attributes;
        $this->attributes = [];

        return null;
    }

    // TODO Oblige un return : IteratorAggregate=>getIterator: Traversable?
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->autoExpires);
    }

    // TODO Oblige un return : Countable=>count: int?
    public function count(): int
    {
        return \count($this->autoExpires);
    }
}
