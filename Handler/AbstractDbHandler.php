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

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use TelNowEdge\FreePBX\Base\Manager\AmpConfManager;

abstract class AbstractDbHandler
{
    /**
     * \Doctrine\DBAL\Connection.
     */
    protected $connection;

    /**
     * \Doctrine\DBAL\Connection.
     */
    protected $cdrConnection;

    protected $eventDispatcher;

    public function setConnection(AmpConfManager $ampConfManager)
    {
        /**
         * Reinit Doctrine connection to inherit of Doctrine instead of FreePBX\DataBase.
         * When I use FreePBX\Database->getDoctrineConnection(), the underlaying class is
         * not manage by doctrine by she is an instance of FreePBX\Database that extends
         * \PDO. So all useful Doctrine\Exceptions aren't available.
         */
        $config = new Configuration();
        $connectionParams = array(
            'dbname' => true === $ampConfManager->exists('AMPDBNAME') ? $ampConfManager->get('AMPDBNAME') : 'asterisk',
            'user' => $ampConfManager->get('AMPDBUSER'),
            'password' => $ampConfManager->get('AMPDBPASS'),
            'host' => true === $ampConfManager->exists('AMPDBHOST') ? $ampConfManager->get('AMPDBHOST') : 'localhost',
            'driver' => 'pdo_mysql',
            'port' => true === $ampConfManager->get('AMPDBPORT') ? $ampConfManager->get('AMPDBPORT') : 3306,
            'charset' => 'utf8',
            'driverOptions' => array(
                1002 => 'SET NAMES utf8',
            ),
        );

        $this->connection = DriverManager::getConnection($connectionParams, $config);
        $this->connection->setFetchMode(\PDO::FETCH_OBJ);

        $connectionParams['dbname'] = true === $ampConfManager->exists('AMPDBCDRNAME') ? $ampConfManager->get('AMPDBCDRNAME') : 'asteriskcdrdb';
        $this->cdrConnection = DriverManager::getConnection($connectionParams, $config);
        $this->cdrConnection->setFetchMode(\PDO::FETCH_OBJ);

        return $this;
    }

    public function setEventDispatcher(\Symfony\Component\EventDispatcher\EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }
}
