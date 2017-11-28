# TelNowEdge/FreepbxBase bundle

## Version

- 2017/11/28 <0.1>: First available working verison

## Install

### With composer require

Currently unavailable

### With git

git clone inside composer vendor dir

```bash
cd /var/www/admin/libraries/Composer/vendor/telnowedge/
git clone freepbx-base

```

Update composer autoload by adding on composer.json

```yaml
"autoload": {
  "psr-4": {
  "TelNowEdge\\FreePBX\\Base\\": "vendor/telnowedge/freepbx-base"
  }
}
```

And finally run

```bash
composer.phar dump-autoload
```

## Overview

This FreepbxBase *bundle* provide an easy way to write FreePBX® modules like an MVC project. He works alone without any modification of FreePBX® core files.

FreepbxBase *bundle* use Symfony® components to improve security, accessibility and support.

He register own namespace to give access on the differents components through several helpers.

FreepbxBase *bundle* introduce in FreePBX® the **Dependency Injection** concept with the Symfony® component. This component is very useful to prevent any `singleton` and share easily your object through your own code.
He provide too the Symfony® **Form** component to validate your form on the server side before to save it on your sql storage.

## Included components

1. [doctrine/annotations](https://github.com/doctrine/annotations)
1. [doctrine/cache](https://github.com/doctrine/cache)
1. [symfony/config](https://github.com/symfony/config)
1. [symfony/dependency-injection](https://github.com/symfony/dependency-injection)
1. [symfony/form](https://github.com/symfony/form)
1. [symfony/http-foundation](https://github.com/symfony/http-foundation)
1. [symfony/security-csrf](https://github.com/symfony/security-csrf)
1. [symfony/twig-bridge](https://github.com/symfony/twig-bridge)
1. [symfony/validator](https://github.com/symfony/validator)
1. [symfony/yaml](https://github.com/symfony/yaml)
1. [symfony/serializer](https://github.com/symfony/serializer)

## How to use

### Start a new FreePBX® module

Start a new FreePBX® module like FreePBX® [practices](https://wiki.freepbx.org/display/FOP/FreePBX+Development) and change only the `extends` class to `TelNowEdge\FreePBX\Base\Module\Module`.

```php
<?php

namespace FreePBX\modules;

use TelNowEdge\FreePBX\Base\Module\Module;

class Foo extends Module implements \BMO
{

}
```

This extends start and bridge all Symfony® components and register a new namespace. Now you can use a PSR4 namespace inside your module.

The new register namespace is `\TelNowEdge\Module`. He is registered with `./modules` base directory. So now you can use `\TelNowEdge\Module\foo` namespace.

**Note:**
> Take care with the case of your module name. Your class can be Foo.class.php but the folder is ./modules/foo. So the namespace is \TelNowEdge\Module\foo.

### Use FreePBX® class like an entry point

Your `Foo.class.php` is the first file for FreePBX®. Now use it to call your logic Controller.

```php
<?php

namespace FreePBX\modules;

use TelNowEdge\FreePBX\Base\Module\Module;
use TelNowEdge\Module\foo\Controller\FooBarController;

class Foo extends Module implements \BMO
{
    public function install()
    {
        $this
            ->get('TelNowEdge\Module\foo\Resources\Migrations\TableMigration')
            ->migrate()
            ;
    }

    public static function myGuiHooks()
    {
        return array('core');
    }

    public function doGuiHook(&$cc)
    {
        $this
            ->processDeviceGui($cc)
            ;
    }

    private function processDeviceGui(&$cc)
    {
        $request = $this->get('request');

        if ('devices' === $request->query->get('display')) {
            if (true === $request->isMethod('POST')) {
                if ('edit' === $request->request->get('action')) {
                    $this->get(FooBarController::class)
                         ->updateAction($request, $cc)
                        ;
                }

                if ('add' === $request->request->get('action')) {
                    $this->get(FooBarController::class)
                         ->createAction($request, $cc)
                        ;
                }
            } else {
                $this->get(FooBarController::class)
                     ->showAction($request, $cc)
                    ;
            }
        }
    }
}
```

Your controller `./modules/foo/Controller/FooBarController.php`

```php
<?php

namespace TelNowEdge\Module\foo\Controller;

use TelNowEdge\FreePBX\Base\Controller\AbstractController;

class FooBarController extends AbstractController
{
    [...]
}

```

## Reference

### Module

Entry point to start FreepbxBase *bundle*.

FreePBX® module must extends `TelNowEdge\FreePBX\Base\Module\Module`

### Controller

Your Controller must extends `TelNowEdge\FreePBX\Base\Controller\AbstractController`

```php
protected $container;

FormFactory function createForm(FormInterface $type, $data = null, array $options = array());

string function render(string $templatePath, array $data = array());

mixed function get(string $service);

```

1. [createForm()](https://symfony.com/doc/current/best_practices/forms.html)

1. [render()](https://symfony.com/doc/current/quick_tour/the_view.html)

    Render return the compile html from the template

1. [get()](http://symfony.com/doc/current/service_container.html)

### Model

### Repository

### DbHandler

### Form

### Dependency Injection

## Todo

1. Increase security in service.yml with publc / private service
1. Create an Acme module
