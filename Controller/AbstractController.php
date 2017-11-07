<?php

namespace TelNowEdge\FreePBX\Base\Controller;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class AbstractController implements ContainerAwareInterface
{
    use ControllerTrait;

    protected $container;

    public function __construct()
    {
        /* $this->twig = $this */
        /*     ->get('template_engine') */
        /*     ->addRegisterPath(static::getViewsDir(), static::getViewsNamespace()) */
        /*     ->getTemplateEngine() */
        /*     ; */
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    abstract public function process($cc);
    abstract public static function getViewsDir();
    abstract public static function getViewsNamespace();
}
