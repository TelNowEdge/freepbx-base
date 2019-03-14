<?php

/*
 * Copyright 2019 TelNowEdge
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

use Doctrine\Common\Collections\ArrayCollection;

class MigrationBuilder
{
    private $collection;

    private function __construct()
    {
        $this->collection = new ArrayCollection();
    }

    public function addMigration(AbstractMigration $class)
    {
        $this->collection->add($class);

        return $this;
    }

    public function removeMigration(AbstractMigration $class)
    {
        $this->collection->remove($class);

        return $this;
    }

    /*
     * Order migrations by date.
     * For same date the order of input array is the reference.
     */
    public function install()
    {
        $ordered = $this->getOrderedMigration();
        $ok = true;

        $ordered->forAll(function ($id, $x) use ($ok) {
            $x->forAll(function ($j, $z) use ($ok, $id) {
                $ok = $ok
                    && $z['object']->playAgainOne($id, $z['migration'])
                    && $z['object']->migrateOne($id, $z['migration'])
                    ;

                return true;
            });

            return true;
        });

        return $ok;
    }

    public function uninstall()
    {
        $ordered = $this->getOrderedUninstall();
        $ok = true;

        $ordered->forAll(function ($id, $x) use ($ok) {
            $x->forAll(function ($j, $z) use ($ok, $id) {
                $ok = $ok
                    && $z['object']->needReinstallOne($id, $z['migration'])
                    && $z['object']->uninstallOne($id, $z['migration'])
                    ;

                return true;
            });

            return true;
        });

        return $ok;
    }

    public static function createBuilder()
    {
        return new self();
    }

    private function getOrderedMigration()
    {
        $migrations = new ArrayCollection();

        $this->collection->forAll(function ($k, $x) use ($migrations) {
            foreach ($x->getOrderedMigration() as $key => $method) {
                if (null === $sub = $migrations->get($key)) {
                    $sub = new ArrayCollection();
                    $migrations->set($key, $sub);
                }

                $sub->add(array(
                    'object' => $x,
                    'migration' => $method,
                ));
            }

            return true;
        });

        $temp = $migrations->toArray();
        ksort($temp);

        return new ArrayCollection($temp);
    }

    private function getOrderedUninstall()
    {
        $migrations = new ArrayCollection();

        $this->collection->forAll(function ($k, $x) use ($migrations) {
            foreach ($x->getOrderedMigration() as $key => $method) {
                if (null === $sub = $migrations->get($key)) {
                    $sub = new ArrayCollection();
                    $migrations->set($key, $sub);
                }

                $sub->add(array(
                    'object' => $x,
                    'migration' => $method,
                ));
            }

            return true;
        });

        $temp = $migrations->toArray();
        krsort($temp);

        return new ArrayCollection($temp);
    }
}
