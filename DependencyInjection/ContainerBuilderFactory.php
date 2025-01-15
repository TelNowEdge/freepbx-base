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

use DirectoryIterator;
use FreePBX;
use SplFileInfo;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder as BaseContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;
use Symfony\Component\Form\DependencyInjection\FormPass;
use Symfony\Component\Validator\DependencyInjection\AddConstraintValidatorsPass;
use TelNowEdge\FreePBX\Base\DependencyInjection\Compiler\ControllerPass;
use TelNowEdgeCachedContainer;

use const PHP_SAPI;

class ContainerBuilderFactory
{
    private static ?self $instance = null;

    private BaseContainerBuilder|TelNowEdgeCachedContainer $container;

    public function __construct(bool $debug = false, bool $disabledCache = false)
    {
        static::autoloadTelNowEdgeModule();
        $this->container = static::startContainer($debug, $disabledCache);
    }

    public static function getInstance(bool $debug = false, bool $disabledCache = false): BaseContainerBuilder|TelNowEdgeCachedContainer
    {
        if (false === isset(static::$instance)) {
            static::$instance = new static($debug, $disabledCache);
        }

        return static::$instance->container;
    }

    public static function dropCache(): bool
    {
        $file = sprintf('%s/../../../../../../assets/cache/container.php', __DIR__);

        if (false === file_exists($file)) {
            return true;
        }

        return unlink($file);
    }

    private function autoloadTelNowEdgeModule(): void
    {
        // SearchHelper: TelNowEdge\Module
        // Autoload to add my own NS starting by TelNowEdge\Module
        spl_autoload_register(function ($class): void {
            if (1 !== preg_match('/^TelNowEdge\\\\Module\\\\(.*)$/', $class, $match)) {
                return;
            }

            $classLoader = preg_replace('/\\\\/', '/', $match[1]);
            require sprintf('%s/../../../../../../modules/%s.php', __DIR__, $classLoader);
        });
    }

    private function startContainer(bool $debug, bool $disabledCache): BaseContainerBuilder|TelNowEdgeCachedContainer
    {
        $action = false === isset($_GET['action']) ? null : $_GET['action'];
        $forceLoading = false;
        $display = false === isset($_GET['display']) ? null : $_GET['display'];

        $file = sprintf('%s/../../../../../../assets/cache/container.php', __DIR__);

        $containerConfigCache = new ConfigCache($file, $debug);

        $argv = $_SERVER['argv']
        ?? [];

        /*
         * Module installation.
         * So disable filter "by active" else I can't load module NS to install it.
         */
        if (
            (PHP_SAPI === 'cli' && [] !== array_intersect(['ma', 'moduleadmin'], $argv))
                || ('modules' === $display && 'process' === $action)
        ) {
            if (file_exists($containerConfigCache->getPath())) {
                unlink($containerConfigCache->getPath());
            }

            $forceLoading = true;
        }

        global $no_auth;
        if (true === $no_auth) {
            $forceLoading = true;
        }

        if (
            false === $containerConfigCache->isFresh()
                || $forceLoading
        ) {
            $container = new BaseContainerBuilder();

            static::registerSelf($container);
            static::registerModule($container, $forceLoading);

            $container
                ->addCompilerPass(
                    new AddConstraintValidatorsPass(),
                    PassConfig::TYPE_BEFORE_OPTIMIZATION,
                    0
                )
                ->addCompilerPass(
                    new FormPass(),
                    PassConfig::TYPE_BEFORE_OPTIMIZATION,
                    0
                )
                ->addCompilerPass(
                    new ControllerPass(),
                    PassConfig::TYPE_BEFORE_OPTIMIZATION,
                    0
                )
                ->addCompilerPass(
                    new RegisterListenersPass(),
                    PassConfig::TYPE_BEFORE_OPTIMIZATION,
                    0
                );

            $container->compile();

            if ($forceLoading || $disabledCache) {
                return $container;
            }

            $dumper = new PhpDumper($container);
            $containerConfigCache->write(
                $dumper->dump(['class' => 'TelNowEdgeCachedContainer']),
                $container->getResources()
            );
        }

        require $file;

        return new TelNowEdgeCachedContainer();
    }

    private function registerSelf(BaseContainerBuilder $container): void
    {
        $c = new BaseExtension();

        $container->registerExtension($c);
        $container->loadFromExtension($c->getAlias());
    }

    private function registerModule(
        BaseContainerBuilder $container,
        bool $forceLoading = false
    ): void {
        $modules = FreePBX::Modules()->getActiveModules(true);

        foreach (new \DirectoryIterator(__DIR__.'/../../../../../../modules/') as $child) {
            if (false === $child->isDir()) {
                continue;
            }

            if (false === $forceLoading && false === isset($modules[$child->getFilename()])) {
                continue;
            }

            $filePath = sprintf(
                '%s/DependencyInjection/%sExtension.php',
                $child->getPathname(),
                ucfirst($child->getFilename())
            );

            $extension = new SplFileInfo($filePath);

            if (true === $extension->isReadable()) {
                $fqdn = sprintf('\TelNowEdge\Module\%s\DependencyInjection\%sExtension', strtolower($child), ucfirst($child));

                $instance = new $fqdn();
                $container->registerExtension($instance);
                $container->loadFromExtension($instance->getAlias());
            }

            $filePath = sprintf(
                '%s/DependencyInjection/%sBundle.php',
                $child->getPathname(),
                ucfirst($child->getFilename())
            );

            $extension = new SplFileInfo($filePath);

            if (true === $extension->isReadable()) {
                $fqdn = sprintf('\TelNowEdge\Module\%s\DependencyInjection\%sBundle', strtolower($child), ucfirst($child));

                $instance = new $fqdn();
                $instance->build($container);
            }
        }
    }
}
