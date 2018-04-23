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

namespace TelNowEdge\FreePBX\Base\Repository;

use Doctrine\DBAL\Connection;

abstract class AbstractRepository
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

    public function setConnection(Connection $connection, Connection $cdrConnection)
    {
        $this->connection = $connection;
        $this->cdrConnection = $cdrConnection;

        return $this;
    }

    protected function fetch(\Doctrine\DBAL\Statement $statment)
    {
        if (false === $res = $statment->fetch()) {
            throw new NoResultException();
        }

        return $res;
    }

    protected function fetchAll(\Doctrine\DBAL\Statement $statment)
    {
        $res = $statment->fetchAll();

        if (true === empty($res)) {
            throw new NoResultException();
        }

        return $res;
    }

    protected function sqlToArray($param)
    {
        if (true === $param instanceof \stdClass) {
            $param = (array) $param;
        }

        $res = array();
        foreach ($param as $key => $value) {
            $chunks = preg_split('/__/', $key);
            $class = $chunks[0];
            $prop = trim($chunks[1], '_');

            $prop = preg_replace_callback('/_(\w)/', function ($match) {
                return ucfirst($match[1]);
            }, $prop);
            $res[$class][$prop] = $value;
        }

        return $res;
    }

    protected function objectFromArray($fqn, array $array)
    {
        $reflector = new \ReflectionClass($fqn);
        $class = $reflector->newInstance();

        foreach ($array as $prop => $value) {
            $method = sprintf('set%s', ucfirst($prop));

            if (true === $reflector->hasMethod($method)) {
                $reflector->getMethod($method)->invoke($class, $value);
            } else {
                throw new \Exception(sprintf('%s:%s is not callable', $fqn, $method));
            }
        }

        return $class;
    }

    protected function uncontrolledObjectFromArray($fqdn, array $array)
    {
        $reflector = new \ReflectionClass($fqdn);
        $class = $reflector->newInstance();

        foreach ($array as $prop => $value) {
            $method = sprintf('set%s', ucfirst($prop));

            call_user_func(array($class, $method), $value);
        }

        return $class;
    }
}
