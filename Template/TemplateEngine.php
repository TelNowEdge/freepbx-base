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

namespace TelNowEdge\FreePBX\Base\Template;

use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Component\Form\FormRenderer;

class TemplateEngine implements TemplateEngineInterface
{
    /**
     * \Twig_Environment.
     */
    private $twig;

    public function __construct(\Symfony\Component\Security\Csrf\CsrfTokenManager $csrfManager)
    {
        $defaultFormTheme = 'freepbx_layout_page.html.twig';

        $appVariableReflection = new \ReflectionClass('\Symfony\Bridge\Twig\AppVariable');
        $vendorTwigBridgeDir = dirname($appVariableReflection->getFileName());

        $twig = new \Twig_Environment(new \Twig_Loader_Filesystem(array(
            $vendorTwigBridgeDir.'/Resources/views/Form',
            __DIR__.'/../Resources/views/Form',
        )));

        $twig->getLoader()->addPath(__DIR__.'/../Resources/views', 'telnowedge');

        $formEngine = new TwigRendererEngine(array($defaultFormTheme), $twig);

        $twig->addRuntimeLoader(new \Twig_FactoryRuntimeLoader(array(
            FormRenderer::class => function () use ($formEngine, $csrfManager) {
                return new FormRenderer($formEngine, $csrfManager);
            },
        )));

        $twig->addExtension(new FormExtension());

        $filter = new \Twig_SimpleFilter('fpbxtrans', function ($string) {
            return _($string);
        });
        $twig->addFilter($filter);

        $filter = new \Twig_SimpleFilter('trans', function ($string) {
            return _($string);
        });
        $twig->addFilter($filter);

        $this->twig = $twig;
    }

    public function addRegisterPath($path, $namespace = \Twig_Loader_Filesystem::MAIN_NAMESPACE)
    {
        $paths = $this->twig->getLoader()->getPaths($namespace);

        if (true === in_array($path, $paths, true)) {
            return $this;
        }

        $this->twig->getLoader()->addPath($path, $namespace);

        return $this;
    }

    public function setRegisterPaths(array $paths, $namespace = \Twig_Loader_Filesystem::MAIN_NAMESPACE)
    {
        $this->twig->getLoader()->setPaths($paths, $namespace);

        return $this;
    }

    public function getTemplateEngine()
    {
        return $this->twig;
    }
}
