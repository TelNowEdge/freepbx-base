<?php

namespace TelNowEdge\FreePBX\Base\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ControllerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (false === $container->has('template_engine')) {
            return;
        }

        $definition = $container->findDefinition('template_engine');

        $taggedServices = $container->findTaggedServiceIds('telnowedge.controller');

        foreach ($taggedServices as $id => $service) {
            $definition->addMethodCall('addRegisterPath', array(
                call_user_func(array($id, 'getViewsDir')),
                call_user_func(array($id, 'getViewsNamespace')),
            ));
        }
    }
}
