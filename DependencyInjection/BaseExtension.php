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

namespace TelNowEdge\FreePBX\Base\DependencyInjection;

use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class BaseExtension extends Extension
{
    /**
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('freepbx.root_dir', __DIR__);

        $this
            ->registerLoggerConfiguration($config, $container, $loader)
            ->registerContainerConfiguration($config, $container, $loader)
            ->registerEventDispatcherConfiguration($config, $container, $loader)
            ->registerFormConfiguration($config, $container, $loader)
            ->registerRequestConfiguration($config, $container, $loader)
            ->registerSecurityCsrfConfiguration($config, $container, $loader)
            ->registerSerializerConfiguration($config, $container, $loader)
            ->registerSessionConfiguration($config, $container, $loader)
            ->registerTemplateEngineConfiguration($config, $container, $loader)
            ->registerAnnotationConfiguration($config, $container, $loader)
            ->registerAttributeConfiguration($config, $container, $loader)
            ->registerValidatorConfiguration($config, $container, $loader)
            ->registerConnectionConfiguration($config, $container, $loader)
            ->registerClientConfiguration($config, $container, $loader);



        $loader->load('services.yml');

    }

    /**
     * @throws Exception
     */
    private function registerClientConfiguration(array $config, ContainerBuilder $container, YamlFileLoader $loader): void
    {
        $loader->load('client.yml');
    }

    /**
     * @throws Exception
     */
    private function registerConnectionConfiguration(array $config, ContainerBuilder $container, YamlFileLoader $loader): static
    {
        $loader->load('connection.yml');

        return $this;
    }

    /**
     * @throws Exception
     */
    private function registerValidatorConfiguration(array $config, ContainerBuilder $container, YamlFileLoader $loader): static
    {
        $loader->load('validator.yml');

        return $this;
    }

    /**
     * @throws Exception
     */
    private function registerAttributeConfiguration(array $config, ContainerBuilder $container, YamlFileLoader $loader): static
    {
        $loader->load('attribute.yml');

        return $this;
    }

    /**
     * @throws Exception
     */
    private function registerAnnotationConfiguration(array $config, ContainerBuilder $container, YamlFileLoader $loader): static
    {
        $loader->load('annotation.yml');

        return $this;
    }

    /**
     * @throws Exception
     */
    private function registerTemplateEngineConfiguration(array $config, ContainerBuilder $container, YamlFileLoader $loader): static
    {
        $loader->load('template_engine.yml');

        return $this;
    }

    /**
     * @throws Exception
     */
    private function registerSessionConfiguration(array $config, ContainerBuilder $container, YamlFileLoader $loader): static
    {
        $loader->load('session.yml');

        return $this;
    }

    /**
     * @throws Exception
     */
    private function registerSerializerConfiguration(array $config, ContainerBuilder $container, YamlFileLoader $loader): static
    {
        $loader->load('serializer.yml');

        return $this;
    }

    /**
     * @throws Exception
     */
    private function registerSecurityCsrfConfiguration(array $config, ContainerBuilder $container, YamlFileLoader $loader): static
    {
        $loader->load('security_csrf.yml');

        return $this;
    }

    /**
     * @throws Exception
     */
    private function registerRequestConfiguration(array $config, ContainerBuilder $container, YamlFileLoader $loader): static
    {
        $loader->load('request.yml');

        return $this;
    }

    /**
     * @throws Exception
     */
    private function registerFormConfiguration(array $config, ContainerBuilder $container, YamlFileLoader $loader): static
    {
        $loader->load('form.yml');

        return $this;
    }

    /**
     * @throws Exception
     */
    private function registerEventDispatcherConfiguration(array $config, ContainerBuilder $container, YamlFileLoader $loader): static
    {
        $loader->load('event_dispatcher.yml');

        return $this;
    }

    /**
     * @throws Exception
     */
    private function registerContainerConfiguration(array $config, ContainerBuilder $container, YamlFileLoader $loader): static
    {
        $loader->load('container.yml');

        return $this;
    }

    /**
     * @throws Exception
     */
    private function registerLoggerConfiguration(array $config, ContainerBuilder $container, YamlFileLoader $loader): static
    {
        $loader->load('logger.yml');

        return $this;
    }
}
