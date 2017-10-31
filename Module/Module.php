<?php

namespace TelNowEdge\FreePBX\Base\Module;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use TelNowEdge\FreePBX\Base\Template\TemplateEngine;
use TelNowEdge\Module\tnehook\Repository\PhoneProvisionRepository;

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

        static::autoloadTelNowEdgeModule();
        $this->startContainer();

        $this->astman = $freepbx->astman;
        $this->config = $freepbx->Config;
        $this->database = $freepbx->Database;
        $this->freepbx = $freepbx;

        $templateHelper = new TemplateEngine();

        $this->request = \TelNowEdge\FreePBX\Base\Http\Request::create();
        $this->formFactory = \TelNowEdge\FreePBX\Base\Form\FormFactory::getInstance();
        $this->validator = \TelNowEdge\FreePBX\Base\Validator\Validator::getInstance();

        $this->twig = $templateHelper
            ->addRegisterPath(static::getViewsDir(), static::getViewsNamespace())
            ->getTemplateEngine()
            ;


    }

    private function startContainer()
    {
        $this->container = $this->createDependencyInjectionContainer();

        $this
            ->registerModuleExtension()
            ->registerSelf()
            ;

        $this->container->compile();
    }

    private function registerModuleExtension()
    {
        $reflection = new \ReflectionClass(static::class);
        $className = $reflection->getShortName();
        $fqdn = sprintf('\TelNowEdge\Module\%s\DependencyInjection\%sExtension', strtolower($className), ucfirst($className));

        call_user_func(array($fqdn, 'load'), $this->container);

        return $this;
    }

    private function registerSelf()
    {
        $c = new \TelNowEdge\FreePBX\Base\DependencyInjection\BaseExtension();
        $c->load($this->container);

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

            require('modules/' . $classLoader . '.php');
        });
    }

    public function createDependencyInjectionContainer()
    {
        return new ContainerBuilder();
    }

    abstract public static function getViewsDir();
    abstract public static function getViewsNamespace();
}
