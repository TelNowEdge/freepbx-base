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

abstract class AbstractDbHandler
{
    /**
     * \Doctrine\DBAL\Connection.
     */
    protected $connection;

    protected $eventDispatcher;

    public function setConnection(\FreePBX\Database $database)
    {
        $this->connection = $database->getDoctrineConnection();
        $this->connection->setFetchMode(\PDO::FETCH_OBJ);

        return $this;
    }

    public function setEventDispatcher(\Symfony\Component\EventDispatcher\EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }
}
