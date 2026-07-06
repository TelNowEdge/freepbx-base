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

namespace TelNowEdge\FreePBX\Base\Repository;

use Exception;
use ReflectionClass;
use ReflectionException;
use TelNowEdge\FreePBX\Base\Manager\AmpConfManager;

abstract class AbstractFileSystemRepository
{
    protected AmpConfManager $ampConfManager;

    public function setAmpConfManager(AmpConfManager $ampConfManager): static
    {
        $this->ampConfManager = $ampConfManager;

        return $this;
    }

    /**
     * @param mixed $fqn
     *
     * @throws ReflectionException
     * @throws Exception
     */
    protected function objectFromArray($fqn, array $array)
    {
        $reflector = new ReflectionClass($fqn);
        $class = $reflector->newInstance();

        foreach ($array as $prop => $value) {
            $method = sprintf('set%s', ucfirst($prop));

            if (true === $reflector->hasMethod($method)) {
                $reflector->getMethod($method)->invoke($class, $value);
            } else {
                throw new Exception(sprintf('%s:%s is not callable', $fqn, $method));
            }
        }

        return $class;
    }
}
