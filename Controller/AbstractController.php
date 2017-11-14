<?php

namespace TelNowEdge\FreePBX\Base\Controller;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class AbstractController implements ContainerAwareInterface
{
    use ControllerTrait;

    protected $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    abstract public static function getViewsDir();
    abstract public static function getViewsNamespace();
}
