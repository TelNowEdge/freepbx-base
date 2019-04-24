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

use Symfony\Component\Console\Application;

abstract class AbstractPhpMigration extends AbstractMigration
{
    protected $application;

    public function setApplication(Application $application)
    {
        $this->application = $application;
    }

    public function migrateOne($id, array $res)
    {
        parent::migrateOne($id, $res);

        if (true === $this->alreadyMigrate($id, static::class)) {
            $this->out(sprintf(
                '[OK]           [%s::%s] Already migrated.',
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

    public function uninstallOne($id, array $res)
    {
        parent::uninstallOne($id, $res);

        if (false === $this->alreadyMigrate($key, static::class)) {
            $this->out(sprintf(
                '[OK]           [%s::%s] Already uninstalled.',
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
            $this->removeMigration($key, static::class);

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

    /*
     * Deprecated
     */
    public function migrate()
    {
        parent::migrate();

        $error = false;
        $methods = $this->getOrderedMigration();

        foreach ($methods as $key => $res) {
            if (true === $this->alreadyMigrate($key, static::class)) {
                $this->out(sprintf('%s already migrate. Nothing todo', $key));
                continue;
            }

            try {
                $res['method']->invoke($this);
                $this->markAsMigrated($key, static::class);
                $this->out(sprintf('Apply migration %s', $key));
            } catch (\Exception $e) {
                $this->out($e->getMessage());
                $error = true;
            }
        }

        return true === $error ? false : true;
    }

    /*
     * Deprecated
     */
    public function uninstall()
    {
        parent::uninstall();

        $error = false;
        $methods = $this->getOrderedUninstall();

        foreach ($methods as $key => $res) {
            if (false === $this->alreadyMigrate($key, static::class)) {
                $this->out(sprintf('%s not currently present. Nothing todo', $key));
                continue;
            }

            try {
                $res['method']->invoke($this);
                $this->removeMigration($key, static::class);
                $this->out(sprintf('Uninstall %s', $key));
            } catch (\Exception $e) {
                $this->out($e->getMessage());
                $error = true;
            }
        }

        return true === $error ? false : true;
    }
}
