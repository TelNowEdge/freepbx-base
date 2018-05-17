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

use TelNowEdge\FreePBX\Base\Manager\UserEventManager;

class UserEventBuilder
{
    protected $userEventManager;

    protected $name;

    protected $type;

    protected $channel;

    protected $values;

    public function __construct(UserEventManager $userEventManager)
    {
        $this->values = new \Doctrine\Common\Collections\ArrayCollection();
        $this->userEventManager = $userEventManager;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    public function getChannel()
    {
        return $this->channel;
    }

    public function setChannel($channel)
    {
        $this->channel = $channel;

        return $this;
    }

    public function getValues()
    {
        return $this->values;
    }

    public function setValues($values)
    {
        $this->values = new \Doctrine\Common\Collections\ArrayCollection($values);

        return $this;
    }

    public function addValue($key, $val)
    {
        $this->values->set($key, $val);

        return $this;
    }

    public function emit()
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
