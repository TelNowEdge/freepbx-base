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

use Doctrine\DBAL\Exception;

/**
 * Transactional error with DDL
 * Please read https://www.doctrine-project.org/projects/doctrine-migrations/en/3.3/explanation/implicit-commits.
 */
abstract class AbstractSqlMigration extends AbstractMigration
{

    // MigrationBuilder -> migrateOne

    /**
     * @throws Exception
     */
    public function migrateOne(string $id, array $res): bool
    {
        /*
         * id = '20240102'
         * res = ['attribute', 'method' (migration2020040901)]
        */
        parent::migrateOne($id, $res);

        if ($this->alreadyMigrate($id, static::class)) {
            $this->skipped->add(sprintf(
                '[SKIPPED]      [%s::%s] Already migrated.',
                $res['method']->class,
                $res['method']->name
            ));

            return true;
        }

        // $this->connection->beginTransaction();
        // $this->cdrConnection->beginTransaction();

        try {
            $sql = $res['method']->invoke($this);
            $this->out(sprintf(
                '[PROCESS]      [%s::%s]: [%s]',
                $res['method']->class, // Nom de la fonction, Ex : migration2020040901
                $res['method']->name, // Nom de la classe, Ex : TrackingMigration
                $sql // Res de la méthode, Ex : migration2020040901()
            ));

            $stmt = $this->{$res['attribute']->connection}->prepare($sql);
            $result = $stmt->executeQuery();
            $result->free();

            $this->markAsMigrated($id, static::class);

            $this->out(sprintf(
                '[OK]           [%s::%s]',
                $res['method']->class,
                $res['method']->name
            ));
        } catch (\Exception $e) {
            $this->out(sprintf(
                '[ERROR]        [%s::%s]: [%s]',
                $res['method']->class,
                $res['method']->name,
                $e->getMessage()
            ));

            // $this->connection->rollBack();
            // $this->cdrConnection->rollBack();

            return false;
        }

        // $this->connection->commit();
        // $this->cdrConnection->commit();

        return true;
    }

    // MigrationBuilder -> uninstallOne
    
    /**
     * @param mixed $id
     *
     * @throws Exception
     */
    public function uninstallOne($id, array $res): bool
    {
        parent::uninstallOne($id, $res);

        $this->connection->beginTransaction();
        $this->cdrConnection->beginTransaction();

        if (false === $this->alreadyMigrate($id, static::class)) {
            $this->out(sprintf(
                '[SKIPPED]      [%s::%s] Already uninstalled.',
                $res['method']->class,
                $res['method']->name
            ));

            return true;
        }

        try {
            $sql = $res['method']->invoke($this);
            $this->out(sprintf(
                '[PROCESS]      [%s::%s]: [%s]',
                $res['method']->class,
                $res['method']->name,
                $sql
            ));

            $stmt = $this->{$res['attribute']->connection}->prepare($sql);
            $result = $stmt->execute();
            $result->free();

            $this->removeMigration($id, static::class);

            $this->out(sprintf(
                '[OK]           [%s::%s]',
                $res['method']->class,
                $res['method']->name
            ));
        } catch (\Exception $e) {
            $this->out(sprintf(
                '[ERROR]        [%s::%s]: [%s]',
                $res['method']->class,
                $res['method']->name,
                $e->getMessage()
            ));

            $this->connection->rollBack();
            $this->cdrConnection->rollBack();

            return false;
        }

        $this->connection->commit();
        $this->cdrConnection->commit();

        return true;
    }
}
