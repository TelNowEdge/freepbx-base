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
use Symfony\Component\Console\Output\ConsoleOutput;

abstract class AbstractMigration
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

    protected $output;

    public function __construct(AnnotationReader $annotationReader)
    {
        $this->annotationReader = $annotationReader;
        $this->output = new ConsoleOutput();
    }

    public function setConnection(Connection $defaultConnection, Connection $cdrConnection)
    {
        $this->connection = $defaultConnection;
        $this->cdrConnection = $cdrConnection;

        return $this;
    }

    public function migrateOne($id, array $res)
    {
        $this->checkDb();
    }

    public function uninstallOne($id, array $res)
    {
        $this->checkDb();
    }

    public function playAgainOne($id, array $res)
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

    public function needReinstallOne($id, array $res)
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

    /*
     * Deprecated
     */
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

    /*
     * Deprecated
     */
    public function playAgain()
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

    /*
     * Deprecated
     */
    public function needReinstall()
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

    public function getOrderedMigration()
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

    /*
     * Deprecated
     */
    protected function getOrderedUninstall()
    {
        @trigger_error('Remove in new version', E_USER_DEPRECATED);

        $reflector = new \ReflectionClass(static::class);
        $methods = $reflector->getMethods();
        $temp = array();

        arsort($methods);

        foreach ($methods as $method) {
            if (1 !== preg_match('/^uninstall(\d{10})$/', $method->name, $match)) {
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
        $stmt = $this->connection->prepare('INSERT INTO `tne_migrations` VALUES (?, ?, NOW())');

        $stmt->bindValue(1, $version);
        $stmt->bindValue(2, $module);

        $stmt->execute();
    }

    protected function removeMigration($version, $module)
    {
        $stmt = $this->connection->prepare('DELETE FROM `tne_migrations` WHERE id = ? AND module = ?');

        $stmt->bindValue(1, $version);
        $stmt->bindValue(2, $module);

        $stmt->execute();
    }

    protected function out($msg)
    {
        $mapping = array(
            'OK' => array(
                'console' => 'info',
                'web' => 'green',
            ),
            'ERROR' => array(
                'console' => 'error',
                'web' => 'red',
            ),
            'PROCESS' => array(
                'console' => 'comment',
                'web' => 'orange',
            ),
            'PLAYAGAIN' => array(
                'console' => 'comment',
                'web' => 'orange',
            ),
        );

        $mode = 'cli' === \PHP_SAPI
            ? 'console'
            : 'web'
            ;

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
            : '<span style="font-weight: bold; color:%1$s;">%2$s</span>'
            ;

        $replacement = sprintf($subject, $mapping[$match[1]][$mode], $match[1]);

        $msg = preg_replace($pattern, $replacement, $msg);

        $this->write($msg);
    }

    private function write($msg)
    {
        if ('cli' === \PHP_SAPI) {
            $this->output->writeln($msg);

            return;
        }

        outn(sprintf('%s<br/>', $msg));
    }
}
