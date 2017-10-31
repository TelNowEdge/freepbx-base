<?php

namespace TelNowEdge\FreePBX\Base\Module;

trait ModuleTrait
{
    protected function createForm($type, $data = null, array $options = array())
    {
        return $this->formFactory->create($type, $data, $options);
    }

    protected function render($template, $data = array())
    {
        $nsTemplate = sprintf('@%s/%s', static::getViewsNamespace(), $template);

        return $this->twig->render($nsTemplate, $data);
    }

    protected function get($service)
    {
        return $this->container->get($service);
    }
}
