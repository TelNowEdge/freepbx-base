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

namespace TelNowEdge\FreePBX\Base\Console;

use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ApplicationFactory implements ContainerAwareInterface
{
    protected $container;

    public function createApplication()
    {
        $commands = $this->getAvailableCommands();

        $application = new Application('telnowedge', '1.0');
        $application->addCommands($commands);

        return $application;
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    private function getAvailableCommands()
    {
        $out = array();
        $modules = \FreePBX::Modules()->getActiveModules();

        foreach ($modules as $module) {
            $path = sprintf(
                '%s/../../../../../../modules/%s/Console/',
                __DIR__,
                $module['rawname']
            );

            try {
                $directoryIterator = new \DirectoryIterator($path);
            } catch (\UnexpectedValueException $e) {
                continue;
            }

            foreach ($directoryIterator as $x) {
                if (true === $x->isDir()) {
                    continue;
                }

                if (1 !== preg_match('/^(.*)\.class\.php$/', $x->getFilename(), $match)) {
                    continue;
                }

                require_once $x->getPathname();
                $class = sprintf('FreePBX\Console\Command\%s', $match[1]);

                $reflection = new \ReflectionClass($class);
                array_push($out, $reflection->newInstanceArgs());
            }
        }

        return $out;
    }
}
