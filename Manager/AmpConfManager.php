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

namespace TelNowEdge\FreePBX\Base\Manager;

use Doctrine\Common\Collections\ArrayCollection;

class AmpConfManager
{
    private ArrayCollection $ampConf;

    public function __construct()
    {
        global $amp_conf;

        $this->ampConf = new ArrayCollection();

        foreach ($amp_conf as $k => $v) {
            $this->ampConf->set($k, $v);
        }
    }

    public function exists(string $param): bool
    {
        return !(null === $this->ampConf->get($param));
    }

    public function get(string $param)
    {
        return $this->ampConf->get($param);
    }
}
