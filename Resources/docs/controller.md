# Controller

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

## ./modules/foo/Controller/FooController

```php
<?php

namespace TelNowEdge\Module\tnehook\Controller;

use Symfony\Component\HttpFoundation\Request;
use TelNowEdge\FreePBX\Base\Controller\AbstractController;
use TelNowEdge\FreePBX\Base\Exception\NoResultException;
use TelNowEdge\Module\foo\Handler\DbHandler\FooDbHandler;
use TelNowEdge\Module\foo\Model\Foo;
use TelNowEdge\Module\foo\Repository\FooRepository;
use TelNowEdge\Module\foo\Type\FooType;

class FooController extends AbstractController
{
    function showAction(Request $request, &$cc)
    {
        if (false === empty($query->get('extdisplay'))) {

            try {
                $foo = $this
                    ->get(FooRepository::class)
                    ->getById($query->get('extdisplay'))
                    ;
            } catch (NoResultException $e) {
                $foo = new Foo();

                throw $e;
            }
        }

        $form = $this->createForm(
            FooType::class,
            $foo
        );

        $html = $this->render('foo/show.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    function createAction(Request $request, &$cc)
    {
        $form = $this->createForm(FooType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $foo = $form->getData();
            $foo->setId($request->request->get('deviceid'));

            $this
                ->get(FooDbHandler::class)
                ->create($foo);
        }

        $html = $this->render('foo/show.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
```
