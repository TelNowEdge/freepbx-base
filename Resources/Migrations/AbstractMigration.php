<?php

namespace TelNowEdge\FreePBX\Base\Resources\Migrations;

abstract class AbstractMigration implements MigrationInterface
{
    /**
     * \FreePBX\Database.
     */
    protected $connection;

    public function setConnection(\FreePBX\Database $database)
    {
        $this->connection = $database->getDoctrineConnection();
        $this->connection->setFetchMode(\PDO::FETCH_OBJ);
    }

    public function migrate()
    {
        $sql = array();
        $reflector = new \ReflectionClass(static::class);
        $methods = $reflector->getMethods();

        asort($methods);
        foreach ($methods as $method) {
            if (1 !== preg_match('/^migration\d{10}$/', $method->name)) {
                continue;
            }

            try {
                $this->connection->executeUpdate($method->invoke($this));
            } catch (\Exception $e) {

            }
        }
    }
}
