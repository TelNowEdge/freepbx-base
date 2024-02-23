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

use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBagInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session as BaseSession;
use Symfony\Component\HttpFoundation\Session\SessionBagInterface;
use Symfony\Component\HttpFoundation\Session\Storage\SessionStorageInterface;

class Session extends BaseSession
{
    protected string $autoExpireName;

    public function __construct(
        SessionStorageInterface $storage = null,
        AttributeBagInterface $attributes = null,
        FlashBagInterface $flashes = null,
        SessionBagInterface $autoExpires = null
    ) {
        parent::__construct($storage, $attributes, $flashes);

        $autoExpires = $autoExpires ?: new AutoExpireBag();
        $this->autoExpireName = $autoExpires->getName();
        $this->registerBag($autoExpires);
    }

    public function getAutoExpireBag(): SessionBagInterface
    {
        return $this->getBag($this->autoExpireName);
    }
}
