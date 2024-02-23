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

use Exception;
use InvalidArgumentException;
use Memcached;
use Symfony\Component\Cache\Adapter\MemcachedAdapter;

class MemcachedFactory
{
    public function createMemcachedManager($connection): ?Memcached
    {
        try {
            $client = MemcachedAdapter::createConnection($this->getConnection($connection));
        } catch (Exception $e) {
            return null;
        }

        return $client;
    }

    private function getConnection($connection): array
    {
        $connections = array(
            'default' => array(
                'memcached://localhost:11211',
            ),
        );

        if (false === isset($connections[$connection])) {
            throw new InvalidArgumentException(sprintf('Unable to find connection: %s', $connection));
        }

        return $connections[$connection];
    }
}
