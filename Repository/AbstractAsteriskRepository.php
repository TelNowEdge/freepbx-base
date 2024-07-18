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

use AGI_AsteriskManager;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Result;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\String\UnicodeString;
use function Symfony\Component\String\u;
use TelNowEdge\FreePBX\Base\Exception\NoResultException;

use function count;

abstract class AbstractAsteriskRepository
{
    public const SQL = '
        SELECT
                a.key a__key
                ,a.value a__value
            FROM
                astdb a
    ';

    /**
     * class AGI_AsteriskManager (libraries/php-asmanager.php).
     */
    protected AGI_AsteriskManager $connection;

    /**
     * \Doctrine\DBAL\Connection.
     */
    protected Connection $asteriskConnection;

    public function setConnection(
        AGI_AsteriskManager $connection,
        Connection $asteriskConnection
    ): void {
        $this->connection = $connection;
        $this->asteriskConnection = $asteriskConnection;
    }

    /**
     * @param mixed $family
     *
     * @throws NoResultException
     */
    public function getByFamily($family): array
    {
        $res = $this->connection->database_show($family);

        if (empty($res)) {
            throw new NoResultException();
        }

        return $res;
    }

    /**
     * @throws NoResultException
     */
    public function show($family, $key): array
    {
        $request = sprintf('%s/%s', $family, $key);
        $res = $this->connection->database_show($request);

        if (empty($res)) {
            throw new NoResultException('No results found for family ' . $family);
        }

        return $res;
    }

    public function sqliteToArray(array $res): array
    {
        $out = [];

        foreach ($res as $x) {
            $out[$x["a__key"]] = $x["a__value"];
        }

        return $this->sqlToArray($out);
    }

    public function sqlToArray(array $res): array
    {
        $temp = [];
        $out = [];

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

    public function linearize($keys, $value): array
    {
        $out = [];

        $array = explode('/', $keys, 2);

        $key = u($array[0])->camel();

        if (1 === count($array)) {
            $value = '' === $value ? null : $value;

            return [(string)$key => $value];
        }

        $out[(string)$key] = $this->linearize($array[1], $value);

        return $out;
    }

    /**
     * @throws NoResultException
     * @throws Exception
     */
    protected function fetch(Result $result): array|false
    {
        if (false === $res = $result->fetchAssociative()) {
            throw new NoResultException();
        }
        return $res;
    }

    /**
     * @throws NoResultException
     * @throws Exception
     */
    protected function fetchAll(Result $result): array
    {
        $res = $result->fetchAllAssociative();

        if (empty($res)) {
            throw new NoResultException();
        }

        return $res;
    }

    /**
     * @param mixed $fqn
     *
     * @throws ReflectionException
     */
    protected function objectFromArray($fqn, array $array): array
    {
        $violations = new ArrayCollection();

        $reflector = new ReflectionClass($fqn);
        $class = $reflector->newInstance();

        foreach ($array as $prop => $value) {
            $method = sprintf('set%s', ucfirst($prop));

            if (true === $reflector->hasMethod($method)) {
                $reflector->getMethod($method)->invoke($class, $value);
            } else {
                $violations->add(sprintf('%s:%s is not callable', $fqn, $method));
            }
        }

        return [
            'object' => $class,
            'violations' => $violations,
        ];
    }
}
