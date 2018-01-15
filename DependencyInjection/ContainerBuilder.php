<?php

namespace TelNowEdge\FreePBX\Base\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder as BaseContainerBuilder;

class ContainerBuilder
{
    private static $instance;

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

        return static::$instance;
    }

    public function getContainer()
    {
        return $this->container;
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

    private function registerModuleExtension(BaseContainerBuilder $container)
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
