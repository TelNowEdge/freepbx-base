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

abstract class AbstractPhpMigration extends AbstractMigration
{
    public function migrate()
    {
        parent::migrate();

        $error = false;
        $methods = $this->getOrderedMigration();

        foreach ($methods as $key => $res) {
            if (true === $this->alreadyMigrate($key, static::class)) {
                continue;
            }

            try {
                $res['method']->invoke($this);
                $this->markAsMigrated($key, static::class);
            } catch (\Exception $e) {
                outn($e->getMessage());
                $error = true;
            }
        }

        return true === $error ? false : true;
    }

    public function uninstall()
    {
        parent::uninstall();

        $error = false;
        $methods = $this->getOrderedUninstall();

        foreach ($methods as $key => $res) {
            if (false === $this->alreadyMigrate($key, static::class)) {
                continue;
            }

            try {
                $res['method']->invoke($this);
                $this->removeMigration($key, static::class);
            } catch (\Exception $e) {
                outn($e->getMessage());
                $error = true;
            }
        }

        return true === $error ? false : true;
    }
}
