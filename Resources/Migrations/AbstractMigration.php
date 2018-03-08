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

namespace TelNowEdge\FreePBX\Base\Resources\Migrations;

use Doctrine\DBAL\Exception\TableNotFoundException;

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
        $this->checkDb();

        $error = false;
        $reflector = new \ReflectionClass(static::class);
        $methods = $reflector->getMethods();

        $this->connection->beginTransaction();

        asort($methods);
        foreach ($methods as $method) {
            if (1 !== preg_match('/^migration(\d{10})$/', $method->name, $match)) {
                continue;
            }

            if (true === $this->alreadyMigrate($match[1], static::class)) {
                continue;
            }

            try {
                $this->connection->executeUpdate($method->invoke($this));
                $this->connection->executeQuery(
                    'INSERT INTO `tne_migrations` VALUES (?, ?, NOW())',
                    array(
                        $match[1],
                        static::class,
                    )
                );
            } catch (\Exception $e) {
                outn($e->getMessage());
                $error = true;
            }
        }

        if (true === $error) {
            $this->connection->rollBack();

            return false;
        }

        $this->connection->commit();

        return true;
    }

    private function checkDb()
    {
        try {
            $this->connection->executeQuery('desc tne_migrations');
        } catch (TableNotFoundException $e) {
            $this->connection->executeQuery('
CREATE
    TABLE
        tne_migrations (
            `id` INT PRIMARY KEY
            ,`module` VARCHAR(255) NOT NULL
            ,created_at DATETIME NOT NULL
        )
            ');
        }
    }

    private function alreadyMigrate($version, $module)
    {
        $stmt = $this->connection->executeQuery('SELECT * FROM tne_migrations WHERE id = ? AND module = ?', array($version, $module));

        return false === $stmt->fetch() ? false : true;
    }
}
