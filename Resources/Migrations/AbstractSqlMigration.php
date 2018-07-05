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

abstract class AbstractSqlMigration extends AbstractMigration
{
    public function migrate()
    {
        parent::migrate();

        $error = false;
        $methods = $this->getOrderedMigration();
        $this->connection->beginTransaction();
        $this->cdrConnection->beginTransaction();

        foreach ($methods as $key => $res) {
            if (true === $this->alreadyMigrate($key, static::class)) {
                $this->out(sprintf('%s already migrate. Nothing todo', $key));

                continue;
            }

            try {
                $sql = $res['method']->invoke($this);
                $this->{$res['annotation'][0]->connection}->executeUpdate($sql);
                $this->markAsMigrated($key, static::class);
                $this->out(sprintf('Apply migration %s: [%s]', $key, $sql));
            } catch (\Exception $e) {
                $this->out($e->getMessage());
                $error = true;
            }
        }

        if (true === $error) {
            $this->connection->rollBack();
            $this->cdrConnection->rollBack();

            return false;
        }

        $this->connection->commit();
        $this->cdrConnection->commit();

        return true;
    }

    public function uninstall()
    {
        parent::uninstall();

        $error = false;
        $methods = $this->getOrderedUninstall();
        $this->connection->beginTransaction();

        foreach ($methods as $key => $res) {
            if (false === $this->alreadyMigrate($key, static::class)) {
                $this->out(sprintf('%s not currently present. Nothing todo', $key));
                continue;
            }

            try {
                $sql = $res['method']->invoke($this);
                $this->connection->executeUpdate($sql);
                $this->removeMigration($key, static::class);
                $this->out(sprintf('Uninstall %s: [%s]', $key, $sql));
            } catch (\Exception $e) {
                $this->out($e->getMessage());
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
}
