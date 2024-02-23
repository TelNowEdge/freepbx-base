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
    private ArrayCollection $collection;

    private function __construct()
    {
        $this->collection = new ArrayCollection();
    }

    public static function createBuilder(): self
    {
        return new self();
    }

    public function addMigration(AbstractMigration $class): static
    {
        $this->collection->add($class);

        return $this;
    }

    /*
     * Order migrations by date.
     * For same date the order of input array is the reference.
     */

    public function removeMigration($class): static
    {
        $this->collection->remove($class);

        return $this;
    }

    public function install(): true
    {
        $ordered = $this->getOrderedMigration();
        $ok = true;

        $ordered->forAll(static function ($id, $x) use ($ok): bool {
            $x->forAll(static function ($j, array $z) use ($ok, $id): bool {
                $ok = $ok
                    && $z['object']->playAgainOne($id, $z['migration'])
                    && $z['object']->migrateOne($id, $z['migration']);

                return true;
            });

            return true;
        });

        // Somme hack. Call the first object to have access to displaySkipped() method
        $this->collection->first()->displaySkipped($this->collection);

        return $ok;
    }

    public function uninstall(): true
    {
        $ordered = $this->getOrderedUninstall();
        $ok = true;

        $ordered->forAll(static function ($id, $x) use ($ok): bool {
            $x->forAll(static function ($j, array $z) use ($ok, $id): bool {
                $ok = $ok
                    && $z['object']->needReinstallOne($id, $z['migration'])
                    && $z['object']->uninstallOne($id, $z['migration']);

                return true;
            });

            return true;
        });

        return $ok;
    }

    private function getOrderedMigration(): ArrayCollection
    {
        $migrations = new ArrayCollection();

        $this->collection->forAll(static function ($k, $x) use ($migrations): bool {
            foreach ($x->getOrderedMigration() as $key => $method) {
                if (null === $sub = $migrations->get($key)) {
                    $sub = new ArrayCollection();
                    $migrations->set($key, $sub);
                }

                $sub->add([
                    'object' => $x,
                    'migration' => $method,
                ]);
            }

            return true;
        });

        $temp = $migrations->toArray();
        ksort($temp);

        return new ArrayCollection($temp);
    }

    private function getOrderedUninstall(): ArrayCollection
    {
        $migrations = new ArrayCollection();

        $this->collection->forAll(static function ($k, $x) use ($migrations): bool {
            foreach ($x->getOrderedMigration() as $key => $method) {
                if (null === $sub = $migrations->get($key)) {
                    $sub = new ArrayCollection();
                    $migrations->set($key, $sub);
                }

                $sub->add([
                    'object' => $x,
                    'migration' => $method,
                ]);
            }

            return true;
        });

        $temp = $migrations->toArray();
        krsort($temp);

        return new ArrayCollection($temp);
    }
}
