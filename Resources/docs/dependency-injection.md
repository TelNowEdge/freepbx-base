# Dependency Injection

Your module must register in the DIC.

To do this `TelNowEdge\FreePBX\Base\Module\Module` call the register DIC function for each modules.

Create the register DIC function

## ./modules/foo/DependencyInjection/FooExtension.php

```php
<?php

namespace TelNowEdge\Module\foo\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Extension\Extension;

class FooExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
    }
}
```

Now please read [Symfony documentation](http://symfony.com/doc/current/components/dependency_injection.html) to add service in the DIC.
