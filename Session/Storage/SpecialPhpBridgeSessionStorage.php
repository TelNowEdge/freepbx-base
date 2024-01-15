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

namespace TelNowEdge\FreePBX\Base\Session\Storage;

use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;

//TODO : MetadataBag was no found without that 'use'
use Symfony\Component\HttpFoundation\Session\Storage\MetadataBag;


class SpecialPhpBridgeSessionStorage extends NativeSessionStorage
{
    public function __construct($handler = null, MetadataBag $metaBag = null)
    {
        // TODO : n'est pas appelé
        // die('bridge php session');
        $this->setMetadataBag($metaBag);
        $this->setSaveHandler($handler);
    }

    public function start(): bool
    {
        if ($this->started) {
            return true;
        }

        if (\PHP_SESSION_ACTIVE === session_status()) {
            $this->loadSession();

            return true;
        }

        parent::start();

        return true;
    }

    public function clear(): void
    {
        // clear out the bags and nothing else that may be set
        // since the purpose of this driver is to share a handler
        foreach ($this->bags as $bag) {
            $bag->clear();
        }

        // reconnect the bags to the session
        $this->loadSession();
    }
}
