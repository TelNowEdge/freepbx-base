<?php

// tests/symfony-container.php

namespace TelNowEdge\FreePBX\Base\DependencyInjection;

use Exception;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 * @internal
 *
 * @coversNothing
 */
final class KernelTest extends Kernel
{
    public function registerBundles(): iterable
    {
        return [];
    }

    /**
     * @throws Exception
     */
    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        // TODO: Implement registerContainerConfiguration() method.
        $loader->load(__DIR__.'/../Resources/config/session.yml');
        $loader->load(__DIR__.'/../Resources/config/security_csrf.yml');
        $loader->load(__DIR__.'/../Resources/config/annotation.yml');
        $loader->load(__DIR__.'/../Resources/config/attribute.yml');
        $loader->load(__DIR__.'/../Resources/config/validator.yml');
        $loader->load(__DIR__.'/../Resources/config/form.yml');
        $loader->load(__DIR__.'/../Resources/config/request.yml');
        $loader->load(__DIR__.'/../Resources/config/template_engine.yml');
        $loader->load(__DIR__.'/../Resources/config/serializer.yml');
        $loader->load(__DIR__.'/../Resources/config/container.yml');
        $loader->load(__DIR__.'/../Resources/config/event_dispatcher.yml');
        $loader->load(__DIR__.'/../Resources/config/connection.yml');
        $loader->load(__DIR__.'/../Resources/config/client.yml');
        $loader->load(__DIR__.'/../Resources/config/logger.yml');
        $loader->load(__DIR__.'/../Resources/config/services.yml');
    }
}

$appKernel = new KernelTest('test', true);
$appKernel->boot();

return $appKernel->getContainer();
