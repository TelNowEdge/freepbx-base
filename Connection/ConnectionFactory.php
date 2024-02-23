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

namespace TelNowEdge\FreePBX\Base\Connection;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception;
use Symfony\Component\Ldap\Ldap;
use TelNowEdge\FreePBX\Base\Manager\AmpConfManager;

class ConnectionFactory
{
    private AmpConfManager $ampConfManager;

    public function __construct(AmpConfManager $ampConfManager)
    {
        $this->ampConfManager = $ampConfManager;
    }

    /**
     * @throws Exception
     */
    public function getDefaultConnection(): Connection
    {
        /**
         * Reinit Doctrine connection to inherit of Doctrine instead of FreePBX\DataBase.
         * When I use FreePBX\Database->getDoctrineConnection(), the underlaying class is
         * not manage by doctrine by she is an instance of FreePBX\Database that extends
         * \PDO. So all useful Doctrine\Exceptions aren't available.
         */
        $config = new Configuration();
        $connectionParams = [
            'dbname' => true === $this->ampConfManager->exists('AMPDBNAME') ? $this->ampConfManager->get('AMPDBNAME') : 'asterisk',
            'user' => $this->ampConfManager->get('AMPDBUSER'),
            'password' => $this->ampConfManager->get('AMPDBPASS'),
            'host' => true === $this->ampConfManager->exists('AMPDBHOST') ? $this->ampConfManager->get('AMPDBHOST') : 'localhost',
            'driver' => 'pdo_mysql',
            'port' => true === $this->ampConfManager->get('AMPDBPORT') ? $this->ampConfManager->get('AMPDBPORT') : 3306,
            'charset' => 'utf8',
            'driverOptions' => [
                1002 => 'SET NAMES utf8',
            ],
        ];

        return DriverManager::getConnection($connectionParams, $config);
    }

    /**
     * @throws Exception
     */
    public function getCdrConnection(): Connection
    {
        /**
         * Reinit Doctrine connection to inherit of Doctrine instead of FreePBX\DataBase.
         * When I use FreePBX\Database->getDoctrineConnection(), the underlaying class is
         * not manage by doctrine by she is an instance of FreePBX\Database that extends
         * \PDO. So all useful Doctrine\Exceptions aren't available.
         */
        $config = new Configuration();
        $connectionParams = [
            'dbname' => true === $this->ampConfManager->exists('AMPDBCDRNAME') ? $this->ampConfManager->get('AMPDBCDRNAME') : 'asteriskcdrdb',
            'user' => $this->ampConfManager->get('AMPDBUSER'),
            'password' => $this->ampConfManager->get('AMPDBPASS'),
            'host' => true === $this->ampConfManager->exists('AMPDBHOST') ? $this->ampConfManager->get('AMPDBHOST') : 'localhost',
            'driver' => 'pdo_mysql',
            'port' => true === $this->ampConfManager->get('AMPDBPORT') ? $this->ampConfManager->get('AMPDBPORT') : 3306,
            'charset' => 'utf8',
            'driverOptions' => [
                1002 => 'SET NAMES utf8',
            ],
        ];

        return DriverManager::getConnection($connectionParams, $config);
    }

    /**
     * @throws Exception
     */
    public function getAddonsConnection(): Connection
    {
        /**
         * Reinit Doctrine connection to inherit of Doctrine instead of FreePBX\DataBase.
         * When I use FreePBX\Database->getDoctrineConnection(), the underlaying class is
         * not manage by doctrine by she is an instance of FreePBX\Database that extends
         * \PDO. So all useful Doctrine\Exceptions aren't available.
         */
        $config = new Configuration();
        $connectionParams = [
            'dbname' => true === $this->ampConfManager->exists('TNE_DBQUEUEMEMBERS') ? $this->ampConfManager->get('TNE_DBQUEUEMEMBERS') : 'tneaddons',
            'user' => $this->ampConfManager->get('AMPDBUSER'),
            'password' => $this->ampConfManager->get('AMPDBPASS'),
            'host' => true === $this->ampConfManager->exists('AMPDBHOST') ? $this->ampConfManager->get('AMPDBHOST') : 'localhost',
            'driver' => 'pdo_mysql',
            'port' => true === $this->ampConfManager->get('AMPDBPORT') ? $this->ampConfManager->get('AMPDBPORT') : 3306,
            'charset' => 'utf8',
            'driverOptions' => [
                1002 => 'SET NAMES utf8',
            ],
        ];

        return DriverManager::getConnection($connectionParams, $config);
    }

    public function getLdapDefaultConnection(): Ldap
    {
        $ldap = Ldap::create('ext_ldap', [
            'host' => $this->ampConfManager->get('TNE_LDAPIP') ?: '127.0.0.1',
            'encryption' => 'yes' === $this->ampConfManager->get('TNE_LDAP_STARTTLS') ? 'tls' : 'none',
        ]);

        $dn = sprintf(
            '%s,%s',
            $this->ampConfManager->get('TNE_LDAPADMIN'),
            $this->ampConfManager->get('TNE_LDAPDN')
        );

        $ldap->bind($dn, $this->ampConfManager->get('TNE_LDAPPWD'));

        return $ldap;
    }

    /**
     * @throws Exception
     */
    public function getAsteriskConnection(): Connection
    {
        $config = new Configuration();
        $connectionParams = [
            'memory' => false,
            'driver' => 'pdo_sqlite',
            'path' => sprintf('%s/astdb.sqlite3', $this->ampConfManager->get('ASTVARLIBDIR')),
        ];

        return DriverManager::getConnection($connectionParams, $config);
    }
}
