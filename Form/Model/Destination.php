<?php

namespace TelNowEdge\FreePBX\Base\Form\Model;

use TelNowEdge\FreePBX\Base\Validator\Constraints as TNEAssert;

/**
 * @TNEAssert\Depends(
 *   field = "destination",
 *   depends = "category",
 *   service = {"TelNowEdge\FreePBX\Base\Helper\DestinationHelper", "getFlatDestinationsByCategory"}
 * )
 */
class Destination
{
    protected $category;

    protected $destination;

    public function __toString()
    {
        return $this->destination;
    }

    public function getDestination()
    {
        return $this->destination;
    }

    public function setDestination($destination)
    {
        $this->destination = $destination;

        return $this;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }
}
