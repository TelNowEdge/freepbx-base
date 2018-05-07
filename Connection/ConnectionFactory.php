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
use Doctrine\DBAL\DriverManager;
use Symfony\Component\Ldap\Ldap;
use TelNowEdge\FreePBX\Base\Manager\AmpConfManager;

class ConnectionFactory
{
    private $ampConfManager;

    public function __construct(AmpConfManager $ampConfManager)
    {
        $this->ampConfManager = $ampConfManager;
    }

    public function getDefaultConnection()
    {
        /**
         * Reinit Doctrine connection to inherit of Doctrine instead of FreePBX\DataBase.
         * When I use FreePBX\Database->getDoctrineConnection(), the underlaying class is
         * not manage by doctrine by she is an instance of FreePBX\Database that extends
         * \PDO. So all useful Doctrine\Exceptions aren't available.
         */
        $config = new Configuration();
        $connectionParams = array(
            'dbname' => true === $this->ampConfManager->exists('AMPDBNAME') ? $this->ampConfManager->get('AMPDBNAME') : 'asterisk',
            'user' => $this->ampConfManager->get('AMPDBUSER'),
            'password' => $this->ampConfManager->get('AMPDBPASS'),
            'host' => true === $this->ampConfManager->exists('AMPDBHOST') ? $this->ampConfManager->get('AMPDBHOST') : 'localhost',
            'driver' => 'pdo_mysql',
            'port' => true === $this->ampConfManager->get('AMPDBPORT') ? $this->ampConfManager->get('AMPDBPORT') : 3306,
            'charset' => 'utf8',
            'driverOptions' => array(
                1002 => 'SET NAMES utf8',
            ),
        );

        $connection = DriverManager::getConnection($connectionParams, $config);
        $connection->setFetchMode(\PDO::FETCH_OBJ);

        return $connection;
    }

    public function getCdrConnection()
    {
        /**
         * Reinit Doctrine connection to inherit of Doctrine instead of FreePBX\DataBase.
         * When I use FreePBX\Database->getDoctrineConnection(), the underlaying class is
         * not manage by doctrine by she is an instance of FreePBX\Database that extends
         * \PDO. So all useful Doctrine\Exceptions aren't available.
         */
        $config = new Configuration();
        $connectionParams = array(
            'dbname' => true === $this->ampConfManager->exists('AMPDBCDRNAME') ? $this->ampConfManager->get('AMPDBCDRNAME') : 'asteriskcdrdb',
            'user' => $this->ampConfManager->get('AMPDBUSER'),
            'password' => $this->ampConfManager->get('AMPDBPASS'),
            'host' => true === $this->ampConfManager->exists('AMPDBHOST') ? $this->ampConfManager->get('AMPDBHOST') : 'localhost',
            'driver' => 'pdo_mysql',
            'port' => true === $this->ampConfManager->get('AMPDBPORT') ? $this->ampConfManager->get('AMPDBPORT') : 3306,
            'charset' => 'utf8',
            'driverOptions' => array(
                1002 => 'SET NAMES utf8',
            ),
        );

        $connection = DriverManager::getConnection($connectionParams, $config);
        $connection->setFetchMode(\PDO::FETCH_OBJ);

        return $connection;
    }

    public function getLdapDefaultConnection()
    {
        $ldap = Ldap::create('ext_ldap', array(
            'host' => $this->ampConfManager->get('TNE_LDAPIP') ?: '127.0.0.1',
            'encryption' => 'yes' === $this->ampConfManager->get('TNE_LDAP_STARTTLS') ? 'tls' : 'none',
        ));

        $dn = sprintf(
            '%s,%s',
            $this->ampConfManager->get('TNE_LDAPADMIN'),
            $this->ampConfManager->get('TNE_LDAPDN')
        );

        $ldap->bind($dn, $this->ampConfManager->get('TNE_LDAPPWD'));

        return $ldap;
    }
}
