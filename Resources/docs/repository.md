# Repository

## ./modules/foo/Resources/config/service.yml

Register your repository in the DIC.

```yml
TelNowEdge\Module\foo\Repository\FooRepository:
  parent: TelNowEdge\FreePBX\Base\Repository\AbstractRepository
  autowire: true
  autoconfigure: false
  public: true
```

## ./modules/foo/Repository/FooRepository

```php
<?php

namespace TelNowEdge\Module\foo\Repository;

use TelNowEdge\FreePBX\Base\Repository\AbstractRepository;
use TelNowEdge\Module\foo\Model\Foo;
use TelNowEdge\Module\foo\Model\Bar;

class FooRepository extends AbstractRepository
{
    const SQL = '
SELECT
        f.id f__id
        ,f.name f__name
        ,f.value f__value
        ,b.id b__id
    FROM
        foo f INNER JOIN bar b
            ON (
            b.id = f.id
        )
';

    public function getCollection()
    {
        $collection = new \Doctrine\Common\Collections\ArrayCollection;

        $stmt = $this->connection->prepare(self::SQL);

        $stmt->execute();

        $res = $this->fetchAll($stmt);

        foreach ($res as $child) {
            $x = $this->sqlToArray($child);
            $collection->add($this->mapModel($x));
        }

        return $collection;
    }

    public function getByid($id)
    {
        $sql = sprintf('%s WHERE f.id = :id', self::SQL);

        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam('id', $id);
        $stmt->execute();

        $res = $this->sqlToArray($this->fetch($stmt));

        return $this->mapModel($res);
    }

    private function mapModel(array $res)
    {
        $f = $this->objectFromArray(Foo::class, $res['f']);
        $b = $this->objectFromArray(Bar::class, $res['b']);

        return $f->setBar($b);
    }
}
```
