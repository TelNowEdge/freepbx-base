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

    public function __construct($freepbx = null)
    {
        parent::__construct($freepbx);

        $this->astman = $freepbx->astman;
        $this->config = $freepbx->Config;
        $this->database = $freepbx->Database;
        $this->freepbx = $freepbx;

        $templateHelper = new TemplateEngine();

        $this->request = \TelNowEdge\FreePBX\Base\Http\Request::create();
        $this->formFactory = \TelNowEdge\FreePBX\Base\Form\FormFactory::getInstance();

        $this->twig = $templateHelper
            ->addRegisterPath(static::getViewsDir(), static::getViewsNamespace())
            ->getTemplateEngine()
            ;
    }

    abstract public static function getViewsDir();
    abstract public static function getViewsNamespace();
}
