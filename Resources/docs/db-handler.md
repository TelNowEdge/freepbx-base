# Db Handler

## ./modules/foo/Resources/config/service.yml

Register your handler in the DIC.

```yml
TelNowEdge\Module\foo\Handler\DbHandler\FooDbHandler:
  parent: TelNowEdge\FreePBX\Base\Handler\AbstractDbHandler;
  autowire: true
  autoconfigure: false
  public: true
```

## ./modules/foo/Handler/DbHandler/FooDbHandler

```php
<?php

namespace TelNowEdge\Module\foo\Handler\DbHandler;

use TelNowEdge\FreePBX\Base\Handler\AbstractDbHandler;
use TelNowEdge\Module\foo\Model\Foo;

class FooDbHandler extends AbstractDbHandler
{
    public function create(Foo $foo)
    {
        $sql = '
INSERT
    INTO
        foo (
            id
            ,name
            ,value
        )
    VALUES (
        :id
        ,:name
        ,:value
    )
';

        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam('id', $foo->getId());
        $stmt->bindParam('name', $foo->getName());
        $stmt->bindParam('value', $foo->getValue());

        $stmt->execute();

        return true;
    }

    public function update(Foo $foo)
    {
        $sql = '
UPDATE
        foo
    SET
        name = :name
        ,value = :value
    WHERE
        id = :id
';

        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam('id', $foo->getId());
        $stmt->bindParam('name', $foo->getName());
        $stmt->bindParam('value', $foo->getValue());

        $stmt->execute();

        return true;
    }
}
```
