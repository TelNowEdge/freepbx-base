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

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Service\Attribute\Required;

abstract class AbstractController implements ControllerInterface
{
    protected ContainerInterface $container;
    protected Request $request;

    // SYMFONY CODE SOURCE 6.4
    #[Required]
    public function setContainer(ContainerInterface $container): ?ContainerInterface
    {
        $previous = $this->container ?? null;
        $this->container = $container;

        return $previous;
    }

    // Controller Interface
    public function setRequest(Request $request): void
    {
        $this->request = $request;
    }

    /**
     * Creates and returns a Form instance from the type of the form.
     */
    protected function createForm(string $type, mixed $data = null, array $options = []): FormInterface
    {
        return $this->container->get('form_factory')->create($type, $data, $options);
    }

    /**
     * Creates and returns a form builder instance.
     */
    protected function createFormBuilder(mixed $data = null, array $options = []): FormBuilderInterface
    {
        return $this->container->get('form_factory')->createBuilder(FormType::class, $data, $options);
    }

    protected function render($template, $data = []): string
    {
        $nsTemplate = sprintf('@%s/%s', static::getViewsNamespace(), $template);

        return $this->container->get('twig')->render($nsTemplate, $data);
    }
}
