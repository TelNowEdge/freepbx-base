<?php

namespace TelNowEdge\FreePBX\Base\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;

abstract class Extension
{
    abstract public function load(ContainerBuilder $container);
}
