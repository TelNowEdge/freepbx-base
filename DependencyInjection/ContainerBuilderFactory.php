<?php

/*
 * Copyright 2026 TelNowEdge
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

declare(strict_types=1);

namespace TelNowEdge\FreePBX\Base\DependencyInjection;

use DirectoryIterator;
use FreePBX;
use RuntimeException;
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

final class ContainerBuilderFactory
{
    private const CACHE_FILE = '/../../../../../../assets/cache/container.php';
    private const MODULES_DIRECTORY = '/../../../../../../modules';

    private static ?self $instance = null;

    private BaseContainerBuilder|TelNowEdgeCachedContainer $container;

    public function __construct(
        bool $debug = false,
        bool $skipCacheWrite = false
    ) {
        self::autoloadTelNowEdgeModule();

        $this->container = self::startContainer($debug, $skipCacheWrite);
    }

    public static function getInstance(
        bool $debug = false,
        bool $skipCacheWrite = false
    ): BaseContainerBuilder|TelNowEdgeCachedContainer {
        self::$instance ??= new self($debug, $skipCacheWrite);

        return self::$instance->container;
    }

    public static function dropCache(): bool
    {
        $file = self::getCacheFile();

        return !is_file($file) || unlink($file);
    }

    private static function getCacheFile(): string
    {
        return __DIR__.self::CACHE_FILE;
    }

    private static function getModulesDirectory(): string
    {
        return __DIR__.self::MODULES_DIRECTORY;
    }

    private static function autoloadTelNowEdgeModule(): void
    {
        spl_autoload_register(static function (string $class): void {
            if (!preg_match(
                '/^TelNowEdge\\\\Module\\\\(.+)$/',
                $class,
                $matches
            )) {
                return;
            }

            $relativePath = str_replace('\\', '/', $matches[1]);
            $file = self::getModulesDirectory().'/'.$relativePath.'.php';

            if (is_file($file)) {
                require_once $file;
            }
        });
    }

    private static function startContainer(
        bool $debug,
        bool $skipCacheWrite,
    ): BaseContainerBuilder|TelNowEdgeCachedContainer {
        $action = $_GET['action'] ?? null;
        $argv = $_SERVER['argv'] ?? [];
        $display = $_GET['display'] ?? null;
        $isAjaxRequest = 'ajax.php' === basename($_SERVER['SCRIPT_FILENAME'] ?? '');

        $cache = new ConfigCache(self::getCacheFile(), $debug);

        $isModuleManagement = (
            PHP_SAPI === 'cli'
            && array_intersect(['ma', 'moduleadmin'], $argv) !== []
        ) || (
            'modules' === $display
            && 'process' === $action
        );

        global $no_auth;

        $isNoAuth = true === $no_auth;

        if ($isModuleManagement && is_file($cache->getPath())) {
            unlink($cache->getPath());
        }

        $mustBuildContainer = !$cache->isFresh();

        if ($mustBuildContainer) {
            $container = new BaseContainerBuilder();

            self::registerSelf($container);

            self::registerModules(
                $container,
                loadInactiveModules: $isNoAuth || $isModuleManagement,
            );

            self::registerCompilerPasses($container);

            $container->compile();

            $shouldWriteCache = !$skipCacheWrite
            && !$isNoAuth
            && !$isAjaxRequest;

            if (!$shouldWriteCache) {
                return $container;
            }

            $dumper = new PhpDumper($container);

            $cache->write(
                $dumper->dump([
                    'class' => TelNowEdgeCachedContainer::class,
                ]),
                $container->getResources(),
            );
        }

        require_once $cache->getPath();

        return new TelNowEdgeCachedContainer();
    }

    private static function registerCompilerPasses(
        BaseContainerBuilder $container
    ): void {
        $passes = [
            new AddConstraintValidatorsPass(),
            new FormPass(),
            new ControllerPass(),
            new RegisterListenersPass(),
        ];

        foreach ($passes as $pass) {
            $container->addCompilerPass(
                $pass,
                PassConfig::TYPE_BEFORE_OPTIMIZATION,
                0
            );
        }
    }

    private static function registerSelf(
        BaseContainerBuilder $container
    ): void {
        $extension = new BaseExtension();

        $container->registerExtension($extension);
        $container->loadFromExtension($extension->getAlias());
    }

    private static function registerModules(
        BaseContainerBuilder $container,
        bool $loadInactiveModules = false
    ): void {
        $activeModules = FreePBX::Modules()->getActiveModules(true);

        foreach (new DirectoryIterator(self::getModulesDirectory()) as $module) {
            if ($module->isDot() || !$module->isDir()) {
                continue;
            }

            $moduleName = $module->getFilename();

            if (!$loadInactiveModules && !isset($activeModules[$moduleName])) {
                continue;
            }

            self::registerModuleExtension(
                $container,
                $module,
                $moduleName
            );

            self::registerModuleBundle(
                $container,
                $module,
                $moduleName
            );
        }
    }

    private static function registerModuleExtension(
        BaseContainerBuilder $container,
        SplFileInfo $module,
        string $moduleName
    ): void {
        $className = ucfirst($moduleName);
        $file = new SplFileInfo(sprintf(
            '%s/DependencyInjection/%sExtension.php',
            $module->getPathname(),
            $className
        ));

        if (!$file->isReadable()) {
            return;
        }

        $fqcn = sprintf(
            'TelNowEdge\\Module\\%s\\DependencyInjection\\%sExtension',
            strtolower($moduleName),
            $className
        );

        if (!class_exists($fqcn)) {
            throw new RuntimeException(sprintf(
                'The module extension class "%s" was not found.',
                $fqcn
            ));
        }

        $extension = new $fqcn();

        $container->registerExtension($extension);
        $container->loadFromExtension($extension->getAlias());
    }

    private static function registerModuleBundle(
        BaseContainerBuilder $container,
        SplFileInfo $module,
        string $moduleName
    ): void {
        $className = ucfirst($moduleName);
        $file = new SplFileInfo(sprintf(
            '%s/DependencyInjection/%sBundle.php',
            $module->getPathname(),
            $className
        ));

        if (!$file->isReadable()) {
            return;
        }

        $fqcn = sprintf(
            'TelNowEdge\\Module\\%s\\DependencyInjection\\%sBundle',
            strtolower($moduleName),
            $className
        );

        if (!class_exists($fqcn)) {
            throw new RuntimeException(sprintf(
                'The module bundle class "%s" was not found.',
                $fqcn
            ));
        }

        $bundle = new $fqcn();
        $bundle->build($container);
    }
}
