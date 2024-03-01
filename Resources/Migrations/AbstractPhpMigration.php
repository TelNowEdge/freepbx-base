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
use Symfony\Component\Console\Application;

abstract class AbstractPhpMigration extends AbstractMigration
{
    protected Application $application;

    public function setApplication(Application $application): void
    {
        $this->application = $application;
    }

    // MigrationBuilder -> migrateOne

    /**
     * @param mixed $id
     *
     * @throws Exception
     */
    public function migrateOne($id, array $res): bool
    {
        parent::migrateOne($id, $res);

        if ($this->alreadyMigrate($id, static::class)) {
            $this->skipped->add(sprintf(
                '[SKIPPED]      [%s::%s] Already migrated.',
                $res['method']->class,
                $res['method']->name
            ));

            return true;
        }

        try {
            $this->out(sprintf(
                '[PROCESS]      [%s::%s]',
                $res['method']->class,
                $res['method']->name
            ));

            $res['method']->invoke($this);
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

            return false;
        }

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

        if (false === $this->alreadyMigrate($id, static::class)) {
            $this->out(sprintf(
                '[SKIPPED]      [%s::%s] Already uninstalled.',
                $res['method']->class,
                $res['method']->name
            ));

            return true;
        }

        try {
            $this->out(sprintf(
                '[PROCESS]      [%s::%s]',
                $res['method']->class,
                $res['method']->name
            ));

            $res['method']->invoke($this);
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

            return false;
        }

        return true;
    }
}
