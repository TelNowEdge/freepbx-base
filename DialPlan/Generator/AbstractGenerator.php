<?php

namespace TelNowEdge\FreePBX\Base\DialPlan\Generator;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class AbstractGenerator implements ContainerAwareInterface
{
    protected $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    abstract public function generate(&$ext, $engine, $priority);
}
