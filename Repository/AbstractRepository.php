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
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Result;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use stdClass;
use Symfony\Component\EventDispatcher\EventDispatcher;
use TelNowEdge\FreePBX\Base\Exception\NoResultException;

use function call_user_func;

abstract class AbstractRepository
{
    /**
     * \Doctrine\DBAL\Connection.
     */
    protected Connection $connection;

    /**
     * \Doctrine\DBAL\Connection.
     */
    protected Connection $cdrConnection;

    /**
     * \Doctrine\DBAL\Connection.
     */
    protected Connection $addonsConnection;

    protected EventDispatcher $eventDispatcher;

    public function setConnection(
        Connection $connection,
        Connection $cdrConnection,
        Connection $addonsConnection
    ): static {
        $this->connection = $connection;
        $this->cdrConnection = $cdrConnection;
        $this->addonsConnection = $addonsConnection;

        return $this;
    }

    public function setEventDispatcher(EventDispatcher $eventDispatcher): void
    {
        $this->eventDispatcher = $eventDispatcher;
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

        if (empty($res)) {
            return [];
        };

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
            return [];
        }

        return $res;
    }

    protected function sqlToArray($param): array
    {
        if ($param instanceof stdClass) {
            $param = (array) $param;
        }

        $res = [];
        foreach ($param as $key => $value) {
            $chunks = explode('__', $key);
            $class = $chunks[0];
            $prop = trim($chunks[1], '_');

            $prop = preg_replace_callback('/_(\w)/', static function (array $match): string {
                return ucfirst($match[1]);
            }, $prop);
            $res[$class][$prop] = $value;
        }

        return $res;
    }

    /**
     * @throws \Exception
     */
    protected function objectFromArray(string $fqn, array $array, array $params = [])
    {
        $reflector = new ReflectionClass($fqn);
        $class = $reflector->newInstanceArgs($params);

        foreach ($array as $prop => $value) {
            $method = sprintf('set%s', ucfirst($prop));
            if (null === $reflectMethod = $reflector->getMethod($method)) {
                throw new \Exception(sprintf('%s:%s is not callable', $fqn, $method));
            }

            if (null === $value) {
                //TODO If many parameters
                $param = $reflectMethod->getParameters()[0];
                $type = $param->getType();

                if (null !== $type && !$type->allowsNull()) {
                    continue;
                }
            }

            $reflectMethod->invoke($class, $value);

        }

        return $class;
    }

    /**
     * @throws ReflectionException
     */
    protected function uncontrolledObjectFromArray($fqdn, array $array, array $params = [])
    {
        $reflector = new ReflectionClass($fqdn);
        $class = $reflector->newInstanceArgs($params);

        foreach ($array as $prop => $value) {
            $method = sprintf('set%s', ucfirst($prop));

            call_user_func([$class, $method], $value);
        }

        return $class;
    }
}
