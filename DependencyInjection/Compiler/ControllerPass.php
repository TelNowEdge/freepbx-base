<?php

/*
 * Copyright [2016] [TelNowEdge]
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace TelNowEdge\FreePBX\Base\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use function call_user_func;

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
            $definition->addMethodCall('addRegisterPath', [
                call_user_func([$id, 'getViewsDir']),
                call_user_func([$id, 'getViewsNamespace']),
            ]);
        }
    }
}
