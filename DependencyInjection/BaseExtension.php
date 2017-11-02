<?php

namespace TelNowEdge\FreePBX\Base\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Extension\Extension;

class BaseExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $this
            ->registerSessionConfiguration($config, $container, $loader)
            ->registerSecurityCsrfConfiguration($config, $container, $loader)
            ->registerFormConfiguration($config, $container, $loader)
            ->registerValidatorConfiguration($config, $container, $loader)
            ->registerRequestConfiguration($config, $container, $loader)
            ->registerTemplateEngineConfiguration($config, $container, $loader)
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
}
