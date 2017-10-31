<?php

namespace TelNowEdge\FreePBX\Base\Repository;

use FreePBX\Database;

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
}
