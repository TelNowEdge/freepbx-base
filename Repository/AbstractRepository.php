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
}
