<?php

namespace TelNowEdge\FreePBX\Base\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class BaseExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $this
            ->registerContainerConfiguration($config, $container, $loader)
            ->registerEventDispatcherConfiguration($config, $container, $loader)
            ->registerFormConfiguration($config, $container, $loader)
            ->registerRequestConfiguration($config, $container, $loader)
            ->registerSecurityCsrfConfiguration($config, $container, $loader)
            ->registerSerializerConfiguration($config, $container, $loader)
            ->registerSessionConfiguration($config, $container, $loader)
            ->registerTemplateEngineConfiguration($config, $container, $loader)
            ->registerValidatorConfiguration($config, $container, $loader)
            ;

        $loader->load('services.yml');
    }

    private function registerSessionConfiguration(array $config, ContainerBuilder $container, YamlFileLoader $loader)
    {
        $loader->load('session.yml');

        return $this;
    }

    private function registerSecurityCsrfConfiguration(array $config, ContainerBuilder $container, YamlFileLoader $loader)
    {
        $loader->load('security_csrf.yml');

        return $this;
    }

    private function registerValidatorConfiguration(array $config, ContainerBuilder $container, YamlFileLoader $loader)
    {
        $loader->load('validator.yml');

        return $this;
    }

    private function registerFormConfiguration(array $config, ContainerBuilder $container, YamlFileLoader $loader)
    {
        $loader->load('form.yml');

        return $this;
    }

    private function registerRequestConfiguration(array $config, ContainerBuilder $container, YamlFileLoader $loader)
    {
        $loader->load('request.yml');

        return $this;
    }

    private function registerTemplateEngineConfiguration(array $config, ContainerBuilder $container, YamlFileLoader $loader)
    {
        $loader->load('template_engine.yml');

        return $this;
    }

    private function registerSerializerConfiguration(array $config, ContainerBuilder $container, YamlFileLoader $loader)
    {
        $loader->load('serializer.yml');

        return $this;
    }

    private function registerContainerConfiguration(array $config, ContainerBuilder $container, YamlFileLoader $loader)
    {
        $loader->load('container.yml');

        return $this;
    }

    private function registerEventDispatcherConfiguration(array $config, ContainerBuilder $container, YamlFileLoader $loader)
    {
        $loader->load('event_dispatcher.yml');

        return $this;
    }

}
