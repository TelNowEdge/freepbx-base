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

namespace TelNowEdge\FreePBX\Base\Manager;

use TelNowEdge\FreePBX\Base\Builder\HintBuilder;
use TelNowEdge\FreePBX\Base\Builder\UserEventBuilder;

class ManagerFactory
{
    protected HintManager $hintManager;

    protected UserEventManager $userEventManager;

    public function setHintManager(HintManager $hintManager): void
    {
        $this->hintManager = $hintManager;
    }

    public function setUserEventManager(UserEventManager $userEventManager): void
    {
        $this->userEventManager = $userEventManager;
    }

    public function createHintBuilder(): HintBuilder
    {
        return new HintBuilder($this->hintManager);
    }

    public function createUserEventBuilder(): UserEventBuilder
    {
        return new UserEventBuilder($this->userEventManager);
    }
}
