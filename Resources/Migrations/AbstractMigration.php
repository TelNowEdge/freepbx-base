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
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Exception\TableNotFoundException;
use ReflectionClass;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Output\ConsoleOutput;
use const PHP_SAPI;

abstract class AbstractMigration
{
    /**
     * \Doctrine\DBAL\Connection.
     */
    protected Connection $connection;

    /**
     * \Doctrine\DBAL\Connection.
     */
    protected Connection $cdrConnection;

    protected ConsoleOutput $output;

    protected ArrayCollection $skipped;

    public function __construct()
    {
        $this->output = new ConsoleOutput();
        $this->skipped = new ArrayCollection();
        $outputStyle = new OutputFormatterStyle('cyan');
        $this->output->getFormatter()->setStyle('skipped', $outputStyle);
    }

    public function setConnection(Connection $defaultConnection, Connection $cdrConnection): static
    {
        $this->connection = $defaultConnection;
        $this->cdrConnection = $cdrConnection;

        return $this;
    }

    // MigrationBuilder -> migrateOne -> (Abstract(...)Migration)parent::migrateOne -> migrateOne
    public function migrateOne($id, array $res)
    {
        $this->checkDb();
    }

    // MigrationBuilder -> migrateOne -> (Abstract(...)Migration)parent::migrateOne -> migrateOne -> checkDb
    // MigrationBuilder -> uninstallOne -> (Abstract(...)Migration)parent::uninstallOne -> uninstallOne -> checkDb
    protected function checkDb(): void
    {
        try {
            $this->connection->executeQuery('desc tne_migrations');
        } catch (TableNotFoundException $e) {
            $this->connection->executeQuery('
                CREATE
                    TABLE
                        tne_migrations (
                            `id` INT
                            ,`module` VARCHAR (255) NOT NULL
                            ,created_at DATETIME NOT NULL
                            ,PRIMARY KEY (
                                `id`
                                ,`module`
                            )
                        )
            ');
        }
    }

    // Deprecated
    // MigrationBuilder -> playAgainOne -> uninstallOne
    // MigrationBuilder -> uninstall -> uninstallOne
    // MigrationBuilder -> uninstallOne -> (Abstract(...)Migration)parent::uninstallOne -> uninstallOne
    public function uninstallOne($id, array $res)
    {
        $this->checkDb();
    }

    // MigrationBuilder -> playAgainOne
    public function playAgainOne($id, array $res): bool
    {
        if (true !== $res['annotation'][0]->playAgain) {
            return true;
        }

        try {
            $this->out(sprintf(
                '[PLAYAGAIN]    [%s::%s]',
                $res['method']->class,
                $res['method']->name
            ));

            $this->removeMigration($id, static::class);
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

    // MigrationBuilder -> playAgainOne -> out
    // MigrationBuilder -> displaySkipped -> out
    // MigrationBuilder -> uninstall -> needReinstallOne
    protected function out($msg): void
    {
        $mapping = [
            'OK' => [
                'console' => 'info',
                'web' => 'green',
            ],
            'SKIPPED' => [
                'console' => 'skipped',
                'web' => 'cyan',
            ],
            'ERROR' => [
                'console' => 'error',
                'web' => 'red',
            ],
            'PROCESS' => [
                'console' => 'comment',
                'web' => 'orange',
            ],
            'PLAYAGAIN' => [
                'console' => 'comment',
                'web' => 'orange',
            ],
        ];

        $mode = 'cli' === PHP_SAPI
            ? 'console'
            : 'web';

        if (1 !== preg_match('/^\[([^\]]+)\]/', $msg, $match)) {
            $this->write($msg);
        }

        if (false === isset($mapping[$match[1]][$mode])) {
            $this->write($msg);

            return;
        }

        $pattern = sprintf('/%s/', $match[1]);
        $subject = 'console' === $mode
            ? '<%1$s>%2$s</%1$s>'
            : '<span style="font-weight: bold; color:%1$s;">%2$s</span>';

        $replacement = sprintf($subject, $mapping[$match[1]][$mode], $match[1]);

        $msg = preg_replace($pattern, $replacement, $msg);

        $this->write($msg);
    }

    // MigrationBuilder -> playAgainOne -> out -> write
    // MigrationBuilder -> displaySkipped -> out -> write
    private function write(iterable|string $msg): void
    {
        if ('cli' === PHP_SAPI) {
            $this->output->writeln($msg);

            return;
        }

        outn(sprintf('%s<br/>', $msg));
    }

    // MigrationBuilder -> playAgainOne -> removeMigration
    // MigrationBuilder -> uninstall -> needReinstallOne -> removeMigration
    // MigrationBuilder -> uninstall -> uninstallOne -> -> removeMigration
    /**
     * @param mixed $version
     * @param mixed $module
     *
     * @throws Exception
     */
    protected function removeMigration($version, $module): void
    {
        if (1 === preg_match('/^99(\d{10})_doLast$/', $version, $match)) {
            $version = $match[1];
        }

        $stmt = $this->connection->prepare('DELETE FROM `tne_migrations` WHERE id = ? AND module = ?');

        $stmt->bindValue(1, $version);
        $stmt->bindValue(2, $module);

        $stmt->executeQuery();
    }

    // MigrationBuilder -> uninstall -> needReinstallOne
    public function needReinstallOne($id, array $res): bool
    {
        if (true !== $res['annotation'][0]->reinstall) {
            return true;
        }

        try {
            $this->out(sprintf(
                '[REINSTALL]    [%s::%s]',
                $res['method']->class,
                $res['method']->name
            ));

            $this->removeMigration($id, static::class);
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

    // Deprecated
    // MigrationBuilder -> displaySkipped
    public function displaySkipped(ArrayCollection $collection): void
    {
        $number = array_reduce($collection->getValues(), static function ($acc, $x): float|int {
            return $acc + $x->skipped->count();
        }, $i = 0);

        $this->out(sprintf(
            '[SKIPPED]      [%03d]',
            $number
        ));
    }

    // Deprecated
    // MigrationBuilder -> (MigrationBuilder)getOrderedMigration -> getOrderedMigration
    // MigrationBuilder -> install -> playAgainOne -> removeMigration -> getOrderedMigration
    // MigrationBuilder -> uninstall -> (MigrationBuilder)getOrderedUninstall -> getOrderedMigration
    public function getOrderedMigration(): array
    {
        $reflector = new ReflectionClass(static::class);
        $methods = $reflector->getMethods();
        $temp = [];
        $last = [];

        asort($methods);

        foreach ($methods as $method) {
            if (1 !== preg_match('/^migration(\d{10})$/', $method->name, $match)) {
                continue;
            }

        /*
         *
         * Plus d'annotations que des attributes (php 8.2)
         *
         *
            $annotation = $this->annotationReader->getMethodAnnotations($method);

            if (true === $annotation[0]->doLast) {
                $last[sprintf('99%s_doLast', $match[1])] = [
                    'annotation' => $annotation,
                    'method' => $method,
                ];

                continue;
            }

            $temp[$match[1]] = [
                'annotation' => $annotation,
                'method' => $method,
            ];

            $annotation = $this->annotationReader->getMethodAnnotations($method);

            if (true === $annotation[0]->doLast) {
                $last[sprintf('99%s_doLast', $match[1])] = [
                    'annotation' => $annotation,
                    'method' => $method,
                ];

                continue;
            }

            $temp[$match[1]] = [
                'annotation' => $annotation,
                'method' => $method,
            ];
        */
        }

        return $temp + $last;
    }

    // Deprecated
    // MigrationBuilder -> migrateOne -> (Abstract(...)Migration)parent::migrateOne -> alreadyMigrate
    // MigrationBuilder -> uninstallOne -> (Abstract(...)Migration)parent::uninstallOne -> alreadyMigrate
    /**
     * @param mixed $version
     * @param mixed $module
     *
     * @throws Exception
     */
    protected function alreadyMigrate($version, $module): bool
    {
        if (1 === preg_match('/^99(\d{10})_doLast$/', $version, $match)) {
            $version = $match[1];
        }

        $stmt = $this->connection->executeQuery(
            'SELECT * FROM tne_migrations WHERE id = ? AND module = ?',
            [
                $version,
                $module,
            ]
        );

        return false !== $stmt->fetchAssociative();
    }

    // MigrationBuilder -> migrateOne -> (Abstract(...)Migration)parent::migrateOne -> markAsMigrated

    /**
     * @throws Exception
     */
    protected function markAsMigrated(mixed $version, mixed $module): void
    {
        if (1 === preg_match('/^99(\d{10})_doLast$/', $version, $match)) {
            $version = $match[1];
        }

        $stmt = $this->connection->prepare('INSERT INTO `tne_migrations` VALUES (?, ?, NOW())');

        $stmt->bindValue(1, $version);
        $stmt->bindValue(2, $module);

        $stmt->executeQuery();
    }
}
