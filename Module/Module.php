<?php

namespace TelNowEdge\FreePBX\Base\Module;

use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;

abstract class Module extends \FreePBX_Helpers
{
    use ModuleTrait;

    protected $astman;
    protected $config;
    protected $database;
    protected $freepbx;

    /**
     * Symfony\Component\HttpFoundation\Request.
     */
    protected $request;

    /**
     * Symfony\Component\Form\FormFactory.
     */
    protected $formFactory;

    /**
     * \Twig_Environment.
     */
    protected $twig;

    /**
     * Symfony\Component\Validator\Validator\RecursiveValidator.php.
     */
    protected $validator;

    /**
     * Symfony\Component\DependencyInjection\ContainerBuilder.
     */
    protected $container;

    public function __construct($freepbx = null)
    {
        parent::__construct($freepbx);

        $this->astman = $freepbx->astman;
        $this->config = $freepbx->Config;
        $this->database = $freepbx->Database;
        $this->freepbx = $freepbx;

        static::autoloadTelNowEdgeModule();
        $this->startContainer();
    }

    private function startContainer()
    {
        $this->container = new ContainerBuilder();

        $this
            ->registerModuleExtension()
            ->registerSelf()
            ;

        $this->container
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

        $this->container->compile();
    }

    private function registerModuleExtension()
    {
        $reflection = new \ReflectionClass(static::class);
        $className = $reflection->getShortName();
        $fqdn = sprintf('\TelNowEdge\Module\%s\DependencyInjection\%sExtension', strtolower($className), ucfirst($className));

        $instance = new $fqdn();

        $this->container->registerExtension($instance);
        $this->container->loadFromExtension($instance->getAlias());

        return $this;
    }

    private function registerSelf()
    {
        $c = new \TelNowEdge\FreePBX\Base\DependencyInjection\BaseExtension();

        $this->container->registerExtension($c);
        $this->container->loadFromExtension($c->getAlias());

        return $this;
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
}
