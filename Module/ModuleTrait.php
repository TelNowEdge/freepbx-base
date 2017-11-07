<?php

namespace TelNowEdge\FreePBX\Base\Module;

trait ModuleTrait
{
    protected function get($service)
    {
        return $this->container->get($service);
    }
}
