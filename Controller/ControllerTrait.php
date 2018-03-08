<?php

/*
 * Copyright [2016] [TelNowEdge]
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

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
