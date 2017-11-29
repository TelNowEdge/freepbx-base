# FreePBX® module

## ./modules/foo/Resources/config/service.yml

Register your controller in the DIC.

**Note:**
> When your register a controller into the DIC don't forget the to add telnowedge.controller tag.

```yml
TelNowEdge\Module\foo\Controller\FooController:
  parent: TelNowEdge\FreePBX\Base\Controller\AbstractController
  autowire: true
  autoconfigure: false
  public: true
  tags:
    - "telnowedge.controller"
```

## ./modules/foo/Foo.class.php

```php
<?php

namespace FreePBX\modules;

use TelNowEdge\FreePBX\Base\Module\Module;
use TelNowEdge\Module\foo\Controller\FooController;

class Foo extends Module implements \BMO
{

    public function install()
    {
        $this
            ->get('TelNowEdge\Module\foo\Resources\Migrations\FooMigration')
            ->migrate()
            ;
    }

    public function uninstall()
    {
    }

    public function backup()
    {
    }

    public function restore($backup)
    {
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
        /**
         * Get Request object from DIC
         * https://api.symfony.com/3.2/Symfony/Component/HttpFoundation/Request.html
         */
        $request = $this->get('request');

        if ('devices' === $request->query->get('display')) {
            if (true === $request->isMethod('POST')) {
                // Get the FooController from DIC
                $this->get(FooController::class)
                     ->createAction($request, $cc)
                    ;

                return;
            }

            $this->get(FooController::class)
                 ->showAction($request, $cc)
                ;
        }
    }
}
```
