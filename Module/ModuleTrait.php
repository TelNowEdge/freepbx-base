<?php

namespace TelNowEdge\FreePBX\Base\Module;

trait ModuleTrait
{
    protected function createForm($type, $data = null, array $options = array())
    {
        return $this->formFactory->create($type, $data, $options);
    }
}
