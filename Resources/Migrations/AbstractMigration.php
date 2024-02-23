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
use Doctrine\DBAL\Exception\TableNotFoundException;
use Exception;
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

    protected AnnotationReader $annotationReader;

    protected ConsoleOutput $output;

    protected ArrayCollection $skipped;

    public function __construct(AnnotationReader $annotationReader)
    {
        $this->annotationReader = $annotationReader;
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

    public function migrateOne($id, array $res)
    {
        $this->checkDb();
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

    public function uninstallOne($id, array $res)
    {
        $this->checkDb();
    }

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
        } catch (Exception $e) {
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

    /*
     * Deprecated
     */

    private function write($msg): void
    {
        if ('cli' === PHP_SAPI) {
            $this->output->writeln($msg);

            return;
        }

        outn(sprintf('%s<br/>', $msg));
    }

    /*
     * Deprecated
     */

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    protected function removeMigration($version, $module): void
    {
        if (1 === preg_match('/^99(\d{10})_doLast$/', $version, $match)) {
            $version = $match[1];
        }

        $stmt = $this->connection->prepare('DELETE FROM `tne_migrations` WHERE id = ? AND module = ?');

        $stmt->bindValue(1, $version);
        $stmt->bindValue(2, $module);

        $stmt->execute();
    }

    /*
     * Deprecated
     */

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
        } catch (Exception $e) {
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

    public function displaySkipped(ArrayCollection $collection): void
    {
        $number = array_reduce($collection->getValues(), function ($acc, $x) {
            return $acc + $x->skipped->count();
        }, $i = 0);

        $this->out(sprintf(
            '[SKIPPED]      [%03d]',
            $number
        ));
    }

    public function migrate()
    {
        @trigger_error('Remove in new version', E_USER_DEPRECATED);
        $this->checkDb();
    }

    /*
     * Deprecated
     */

    public function uninstall()
    {
        @trigger_error('Remove in new version', E_USER_DEPRECATED);
        $this->checkDb();
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function playAgain(): bool
    {
        @trigger_error('Remove in new version', E_USER_DEPRECATED);

        $this->checkDb();

        $error = false;
        $methods = $this->getOrderedMigration();
        $this->connection->beginTransaction();

        foreach ($methods as $key => $res) {
            if (1 !== preg_match('/^migration(\d{10})$/', $res['method']->name)) {
                continue;
            }

            if (true !== $res['annotation'][0]->playAgain) {
                continue;
            }

            try {
                $this->removeMigration($key, static::class);
                $this->out(sprintf('%s marked for playAgain', $key));
            } catch (Exception $e) {
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

    public function getOrderedMigration(): array
    {
        $reflector = new ReflectionClass(static::class);
        $methods = $reflector->getMethods();
        $temp = array();
        $last = array();

        asort($methods);

        foreach ($methods as $method) {
            if (1 !== preg_match('/^migration(\d{10})$/', $method->name, $match)) {
                continue;
            }

            $annotation = $this->annotationReader->getMethodAnnotations($method);

            if (true === $annotation[0]->doLast) {
                $last[sprintf('99%s_doLast', $match[1])] = array(
                    'annotation' => $annotation,
                    'method' => $method,
                );

                continue;
            }

            $temp[$match[1]] = array(
                'annotation' => $annotation,
                'method' => $method,
            );
        }

        return $temp + $last;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function needReinstall(): bool
    {
        @trigger_error('Remove in new version', E_USER_DEPRECATED);

        $this->checkDb();

        $error = false;
        $methods = $this->getOrderedMigration();
        $this->connection->beginTransaction();

        foreach ($methods as $key => $res) {
            if (1 !== preg_match('/^migration(\d{10})$/', $res['method']->name)) {
                continue;
            }

            if (true !== $res['annotation'][0]->reinstall) {
                continue;
            }

            try {
                $this->removeMigration($key, static::class);
                $this->out(sprintf('%s marked for reinstall', $key));
            } catch (Exception $e) {
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

    protected function getOrderedUninstall(): array
    {
        @trigger_error('Remove in new version', E_USER_DEPRECATED);

        $reflector = new ReflectionClass(static::class);
        $methods = $reflector->getMethods();
        $temp = [];

        arsort($methods);

        foreach ($methods as $method) {
            if (1 !== preg_match('/^uninstall(\d{10})$/', $method->name, $match)) {
                continue;
            }

            $temp[$match[1]] = [
                'annotation' => $this->annotationReader->getMethodAnnotations($method),
                'method' => $method,
            ];
        }

        return $temp;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
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

        return !(false === $stmt->fetch());
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    protected function markAsMigrated($version, $module): void
    {
        if (1 === preg_match('/^99(\d{10})_doLast$/', $version, $match)) {
            $version = $match[1];
        }

        $stmt = $this->connection->prepare('INSERT INTO `tne_migrations` VALUES (?, ?, NOW())');

        $stmt->bindValue(1, $version);
        $stmt->bindValue(2, $module);

        $stmt->execute();
    }
}
