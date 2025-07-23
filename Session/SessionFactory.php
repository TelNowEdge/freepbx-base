<?php

/*
 * Copyright 2025 TelNowEdge
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

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Storage\SessionStorageInterface;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBagInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionBagInterface;

class SessionFactory
{
    private SessionStorageInterface $storage;
    private AttributeBagInterface $attributes;
    private FlashBagInterface $flashes;
    private SessionBagInterface $autoExpires;

    public function __construct(
        SessionStorageInterface $storage,
        AttributeBagInterface $attributes,
        FlashBagInterface $flashes,
        SessionBagInterface $autoExpires
    ) {
        $this->storage = $storage;
        $this->attributes = $attributes;
        $this->flashes = $flashes;
        $this->autoExpires = $autoExpires;
    }

    public function create(): SessionInterface
    {
        if (PHP_SAPI === 'cli') {
            return new NullSession();
        }

        return new Session(
            $this->storage,
            $this->attributes,
            $this->flashes,
            $this->autoExpires
        );
    }
}
