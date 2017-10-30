<?php

namespace TelNowEdge\FreePBX\Base\Module;

use TelNowEdge\FreePBX\Base\Template\TemplateEngine;

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

    public function __construct($freepbx = null)
    {
        parent::__construct($freepbx);

        static::autoloadTelNowEdgeModule();

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

    abstract public static function getViewsDir();
    abstract public static function getViewsNamespace();
}
