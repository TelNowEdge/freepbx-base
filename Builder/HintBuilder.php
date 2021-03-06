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

use TelNowEdge\FreePBX\Base\Manager\HintManager;

class HintBuilder
{
    protected $hintManager;

    protected $name;

    protected $extension;

    protected $status;

    public function __construct(HintManager $hintManager)
    {
        $this->hintManager = $hintManager;
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

    public function getExtension()
    {
        return $this->extension;
    }

    public function setExtension($extension)
    {
        $this->extension = $extension;

        return $this;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    public function create()
    {
        return $this->hintManager
            ->create(
                $this->name,
                $this->extension
            );
    }

    public function update()
    {
        return $this->hintManager
            ->update(
                $this->name,
                $this->status
            );
    }
}
