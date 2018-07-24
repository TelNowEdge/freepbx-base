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
use Symfony\Component\Ldap\Ldap;
use TelNowEdge\FreePBX\Base\Manager\AmpConfManager;

abstract class AbstractLdapHandler
{
    protected $ampConfManager;

    protected $connection;

    protected $eventDispatcher;

    public function setAmpConfManager(AmpConfManager $ampConfManager)
    {
        $this->ampConfManager = $ampConfManager;

        return $this;
    }

    public function setConnection(Ldap $connection)
    {
        $this->connection = $connection;

        return $this;
    }

    public function setEventDispatcher(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;

        return $this;
    }

    protected function getFqdn()
    {
        return $this->ampConfManager->get('TNE_LDAPDN');
    }

    protected function getUserSchemaName()
    {
        return $this->ampConfManager->get('TNE_LDAP_SCHEMA_USER');
    }

    protected function getGroupSchemaName()
    {
        return $this->ampConfManager->get('TNE_LDAP_SCHEMA_GROUP');
    }

    protected function getDirectorySchemaName()
    {
        return $this->ampConfManager->get('TNE_LDAP_SCHEMA_DIRECTORY');
    }

    protected function getPrivateDirectorySchemaName()
    {
        return $this->ampConfManager->get('TNE_LDAP_SCHEMA_PRIVATE_DIRECTORY');
    }

    protected function getEntryManager()
    {
        return $this->connection->getEntryManager();
    }
}
