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
use TelNowEdge\FreePBX\Base\Exception\NoResultException;

abstract class AbstractAsteriskRepository
{
    const SQL = '
SELECT
        a.key a__key
        ,a.value a__value
    FROM
        astdb a
';

    /**
     * class AGI_AsteriskManager (libraries/php-asmanager.php).
     */
    protected $connection;

    /**
     * \Doctrine\DBAL\Connection.
     */
    protected $asteriskConnection;

    public function setConnection(
        \AGI_AsteriskManager $connection,
        Connection $asteriskConnection
    ) {
        $this->connection = $connection;
        $this->asteriskConnection = $asteriskConnection;
    }

    public function getByFamily($family)
    {
        $res = $this->connection
            ->database_show($family);

        if (true === empty($res)) {
            throw new NoResultException();
        }

        return $res;
    }

    public function show($family, $key)
    {
        $request = sprintf('%s/%s', $family, $key);

        $res = $this->connection
            ->database_show($request);

        if (true === empty($res)) {
            throw new NoResultException();
        }

        return $res;
    }

    public function linearize($keys, $value)
    {
        $out = array();

        $array = preg_split('#/#', $keys, 2);

        $key = \Doctrine\Common\Util\Inflector::camelize($array[0]);

        if (1 === count($array)) {
            $value = '' === $value ? null : $value;

            return array($key => $value);
        }

        $out[$key] = $this->linearize($array[1], $value);

        return $out;
    }

    public function sqliteToArray(array $res)
    {
        $out = array();

        foreach ($res as $x) {
            $out[$x->a__key] = $x->a__value;
        }

        return $this->sqlToArray($out);
    }

    public function sqlToArray(array $res)
    {
        $temp = array();
        $out = array();

        foreach ($res as $key => $child) {
            $key = strtolower($key);

            $tld = preg_split('#/#', $key, 3, PREG_SPLIT_NO_EMPTY);

            if (false === isset($tld[2])) {
                continue;
            }

            $x = $this->linearize($tld[2], $child);

            $temp = array_merge_recursive($temp, $x);

            $out[$tld[0]] = $temp;
            $out[$tld[0]]['id'] = $tld[1];
        }

        return $out;
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

    protected function objectFromArray($fqn, array $array)
    {
        $violations = new \Doctrine\Common\Collections\ArrayCollection();

        $reflector = new \ReflectionClass($fqn);
        $class = $reflector->newInstance();

        foreach ($array as $prop => $value) {
            $method = sprintf('set%s', ucfirst($prop));

            if (true === $reflector->hasMethod($method)) {
                $reflector->getMethod($method)->invoke($class, $value);
            } else {
                $violations->add(sprintf('%s:%s is not callable', $fqn, $method));
            }
        }

        return array(
            'object' => $class,
            'violations' => $violations,
        );
    }
}
