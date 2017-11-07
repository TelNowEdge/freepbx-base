<?php

namespace TelNowEdge\FreePBX\Base\Controller;

trait ControllerTrait
{
    protected function createForm($type, $data = null, array $options = array())
    {
        return $this->get('form_factory')->create($type, $data, $options);
    }

    protected function render($template, $data = array())
    {
        $nsTemplate = sprintf('@%s/%s', static::getViewsNamespace(), $template);

        return $this->get('twig')->render($nsTemplate, $data);
    }

    protected function get($service)
    {
        return $this->container->get($service);
    }
}
