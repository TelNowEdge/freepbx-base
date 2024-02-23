<?php

namespace TelNowEdge\FreePBX\Base\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerInterface;

class container
{
    protected $container;

    public function setContainer(ContainerInterface $container = null): ?ContainerInterface
    {
        return $this->container = $container;
    }
}
