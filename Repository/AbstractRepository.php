<?php

namespace TelNowEdge\FreePBX\Base\Repository;

use FreePBX\Database;
use TelNowEdge\FreePBX\Base\Exception\NoResultException;

abstract class AbstractRepository
{
    /**
     * \Doctrine\DBAL\Connection
     */
    protected $connection;

    public function setConnection(\FreePBX\Database $database)
    {
        $this->connection = $database->getDoctrineConnection();
        $this->connection->setFetchMode(\PDO::FETCH_OBJ);

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

}
