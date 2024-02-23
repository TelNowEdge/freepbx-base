<?php

/*
 * Copyright [2018] [TelNowEdge]
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

use Symfony\Component\EventDispatcher\EventDispatcher;
use TelNowEdge\FreePBX\Base\Manager\AmpConfManager;

abstract class AbstractFileSystemHandler
{
    protected AmpConfManager $ampConfManager;

    protected EventDispatcher $eventDispatcher;

    public function setAmpConfManager(AmpConfManager $ampConfManager): static
    {
        $this->ampConfManager = $ampConfManager;

        return $this;
    }

    public function setEventDispatcher(EventDispatcher $eventDispatcher): void
    {
        $this->eventDispatcher = $eventDispatcher;
    }
}
