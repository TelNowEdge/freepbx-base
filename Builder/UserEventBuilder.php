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

namespace TelNowEdge\FreePBX\Base\Builder;

use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use TelNowEdge\FreePBX\Base\Manager\UserEventManager;

class UserEventBuilder
{

    protected string $name;

    protected $type;

    protected $channel;

    protected ArrayCollection $values;

    public function __construct(protected UserEventManager $userEventManager)
    {
        $this->values = new ArrayCollection();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getChannel()
    {
        return $this->channel;
    }

    public function setChannel($channel): static
    {
        $this->channel = $channel;

        return $this;
    }

    public function getValues(): ArrayCollection
    {
        return $this->values;
    }

    public function setValues($values): static
    {
        $this->values = new ArrayCollection($values);

        return $this;
    }

    public function addValue($key, $val): static
    {
        $this->values->set($key, $val);

        return $this;
    }

    /**
     * @throws Exception
     */
    public function emit(): true
    {
        return $this->userEventManager
            ->emit(
                $this->name,
                $this->type,
                $this->channel,
                $this->values
            );
    }
}
