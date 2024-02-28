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

use Exception;
use TelNowEdge\FreePBX\Base\Manager\HintManager;

class HintBuilder
{
    protected string $name;

    protected $extension;

    protected $status;

    public function __construct(protected HintManager $hintManager) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getExtension()
    {
        return $this->extension;
    }

    public function setExtension($extension): static
    {
        $this->extension = $extension;

        return $this;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @throws Exception
     */
    public function create(): true
    {
        return $this->hintManager
            ->create(
                $this->name,
                $this->extension
            );
    }

    /**
     * @throws Exception
     */
    public function update(): true
    {
        return $this->hintManager
            ->update(
                $this->name,
                $this->status
            );
    }
}
