# Model

## ./modules/foo/Model/Foo.php

The model isn't register in the DIC. Model by conception must be *alone*.

`@Assert` annotation set validators on field. Validator was used by `Form` component.

```php
<?php

namespace TelNowEdge\Module\foo\Model;

use Symfony\Component\Validator\Constraints as Assert;

class Foo
{
    /**
     * @Assert\NotNull()
     * @Assert\Type("integer")
     */
    protected $id;

    /**
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    protected $name;

    /**
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    protected $value;

    /**
     *
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     *
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     *
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     *
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     *
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }
}
```
