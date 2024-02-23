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

namespace TelNowEdge\FreePBX\Base\Handler;

use AGI_AsteriskManager;
use Symfony\Component\EventDispatcher\EventDispatcher;

abstract class jAbstractAstHandler
{
    /**
     * class AGI_AsteriskManager (libraries/php-asmanager.php).
     */
    protected AGI_AsteriskManager $connection;

    protected EventDispatcher $eventDispatcher;

    public function setConnection(AGI_AsteriskManager $connection): void
    {
        $this->connection = $connection;
    }

    public function setEventDispatcher(EventDispatcher $eventDispatcher): void
    {
        $this->eventDispatcher = $eventDispatcher;
    }
}
