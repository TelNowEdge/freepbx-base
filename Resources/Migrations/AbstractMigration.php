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

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\TableNotFoundException;

abstract class AbstractMigration implements MigrationInterface
{
    /**
     * \Doctrine\DBAL\Connection.
     */
    protected $connection;

    /**
     * \Doctrine\DBAL\Connection.
     */
    protected $cdrConnection;

    protected $annotationReader;

    public function __construct(AnnotationReader $annotationReader)
    {
        $this->annotationReader = $annotationReader;
    }

    public function setConnection(Connection $defaultConnection, Connection $cdrConnection)
    {
        $this->connection = $defaultConnection;
        $this->cdrConnection = $cdrConnection;

        return $this;
    }

    public function migrate()
    {
        $this->checkDb();
    }

    public function uninstall()
    {
        $this->checkDb();
    }

    public function needReinstall()
    {
        $this->checkDb();

        $error = false;
        $methods = $this->getOrderedMigration();
        $this->connection->beginTransaction();

        foreach ($methods as $key => $res) {
            if (1 !== preg_match('/^migration(\d{10})$/', $res['method']->name)) {
                continue;
            }

            if (true === $res['annotation'][0]->reinstall) {
                try {
                    $this->removeMigration($key, static::class);
                    $this->out(sprintf('%s marked for reinstall', $key));
                } catch (\Exception $e) {
                    $this->out($e->getMessage());
                    $error = true;
                }
            }
        }

        if (true === $error) {
            $this->connection->rollBack();

            return false;
        }

        $this->connection->commit();

        return true;
    }

    protected function getOrderedUninstall()
    {
        $reflector = new \ReflectionClass(static::class);
        $methods = $reflector->getMethods();
        $temp = array();

        arsort($methods);

        foreach ($methods as $method) {
            if (1 !== preg_match('/^uninstall(\d{10})$/', $method->name, $match)) {
                continue;
            }

            $temp[$match[1]] = $method;
        }

        return $temp;
    }

    protected function getOrderedMigration()
    {
        $reflector = new \ReflectionClass(static::class);
        $methods = $reflector->getMethods();
        $temp = array();

        asort($methods);

        foreach ($methods as $method) {
            if (1 !== preg_match('/^migration(\d{10})$/', $method->name, $match)) {
                continue;
            }

            $temp[$match[1]] = array(
                'annotation' => $this->annotationReader->getMethodAnnotations($method),
                'method' => $method,
            );
        }

        return $temp;
    }

    protected function checkDb()
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

    protected function alreadyMigrate($version, $module)
    {
        $stmt = $this->connection->executeQuery(
            'SELECT * FROM tne_migrations WHERE id = ? AND module = ?',
            array(
                $version,
                $module,
            )
        );

        return false === $stmt->fetch() ? false : true;
    }

    protected function markAsMigrated($version, $module)
    {
        $this->connection->executeQuery(
            'INSERT INTO `tne_migrations` VALUES (?, ?, NOW())',
            array(
                $version,
                $module,
            )
        );
    }

    protected function removeMigration($version, $module)
    {
        $this->connection->executeQuery(
            'DELETE FROM `tne_migrations` WHERE id = ? AND module = ?',
            array(
                $version,
                $module,
            )
        );
    }

    protected function out($msg)
    {
        $separator = '<br />';

        if ('cli' === PHP_SAPI) {
            $separator = "\n";
        }

        outn(sprintf('%s%s', $msg, $separator));
    }
}
