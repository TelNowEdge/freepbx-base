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

/**
 * Please read this article to understand why this is a ContainerBuilderFactory
 * and not just a ContainerBuilder.
 *
 * https://bugs.php.net/bug.php?id=55068
 * http://blog.mageekbox.net/?post/2011/07/14/Espace-de-noms-et-importations-de-classes
 */

namespace TelNowEdge\FreePBX\Base\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder as BaseContainerBuilder;

class ContainerBuilderFactory
{
    private static $instance;

    private $container;

    public function __construct()
    {
        static::autoloadTelNowEdgeModule();
        $this->container = static::startContainer();
    }

    public static function getInstance()
    {
        if (false === isset(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance->container;
    }

    private static function autoloadTelNowEdgeModule()
    {
        // SearchHelper: TelNowEdge\Module
        // Autoload to add my own NS starting by TelNowEdge\Module
        spl_autoload_register(function ($class) {
            if (1 !== preg_match('/^TelNowEdge\\\\Module\\\\(.*)$/', $class, $match)) {
                return;
            }

            $classLoader = preg_replace('/\\\\/', '/', $match[1]);

            require 'modules/'.$classLoader.'.php';
        });
    }

    private static function startContainer()
    {
        $container = new BaseContainerBuilder();

        static::registerSelf($container);
        static::registerModuleExtension($container);

        $container
            ->addCompilerPass(
                new \Symfony\Component\Validator\DependencyInjection\AddConstraintValidatorsPass(),
                PassConfig::TYPE_BEFORE_OPTIMIZATION,
                0
            )
            ->addCompilerPass(
                new \Symfony\Component\Form\DependencyInjection\FormPass(),
                PassConfig::TYPE_BEFORE_OPTIMIZATION,
                0
            )
            ->addCompilerPass(
                new \TelNowEdge\FreePBX\Base\DependencyInjection\Compiler\ControllerPass(),
                PassConfig::TYPE_BEFORE_OPTIMIZATION,
                0
            )
            ->addCompilerPass(
                new \Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass(),
                PassConfig::TYPE_BEFORE_OPTIMIZATION,
                0
            )
            ;

        $container->compile();

        return $container;
    }

    private static function registerSelf(BaseContainerBuilder $container)
    {
        $c = new \TelNowEdge\FreePBX\Base\DependencyInjection\BaseExtension();

        $container->registerExtension($c);
        $container->loadFromExtension($c->getAlias());
    }

    private static function registerModuleExtension(BaseContainerBuilder $container)
    {
        foreach (new \DirectoryIterator(__DIR__.'/../../../../../../modules/') as $child) {
            if (false === $child->isDir()) {
                continue;
            }

            $filePath = sprintf(
                '%s/DependencyInjection/%sExtension.php',
                $child->getPathname(),
                ucfirst($child->getFilename())
            );

            $extension = new \SplFileInfo($filePath);

            if (false === $extension->isReadable()) {
                continue;
            }

            $fqdn = sprintf('\TelNowEdge\Module\%s\DependencyInjection\%sExtension', strtolower($child), ucfirst($child));

            $instance = new $fqdn();
            $container->registerExtension($instance);
            $container->loadFromExtension($instance->getAlias());
        }
    }
}
