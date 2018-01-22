<?php

namespace TelNowEdge\FreePBX\Base\Module;

use TelNowEdge\FreePBX\Base\DependencyInjection\ContainerBuilderFactory;

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
        $this->container = ContainerBuilderFactory::getInstance();
    }
}
