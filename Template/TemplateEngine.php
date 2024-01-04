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

        $twig = new \Twig\Environment(new \Twig\Loader\FilesystemLoader(array(
            $vendorTwigBridgeDir.'/Resources/views/Form',
            __DIR__.'/../Resources/views/Form',
        )), array(
            // 'cache' => sprintf('%s/../../../../../../assets/cache/twig/', __DIR__),
        ));

        $twig->getLoader()->addPath(__DIR__.'/../Resources/views', 'telnowedge');

        $formEngine = new TwigRendererEngine(array($defaultFormTheme), $twig);

        $twig->addRuntimeLoader(new \Twig\RuntimeLoader\FactoryRuntimeLoader(array(
            FormRenderer::class => function () use ($formEngine, $csrfManager) {
                return new FormRenderer($formEngine, $csrfManager);
            },
        )));

        $twig->addExtension(new FormExtension());

        $filter = new \Twig\TwigFilter('fpbxtrans', function ($string) {
            return _($string);
        });
        $twig->addFilter($filter);

        $filter = new \Twig\TwigFilter('trans', function ($string) {
            return _($string);
        });
        $twig->addFilter($filter);

        $this->twig = $twig;
    }

    public function addRegisterPath($path, $namespace = \Twig\Loader\FilesystemLoader::MAIN_NAMESPACE)
    {
        if (true === empty($path) || true === empty($namespace)) {
            return $this;
        }

        $paths = $this->twig->getLoader()->getPaths($namespace);

        if (true === in_array($path, $paths, true)) {
            return $this;
        }

        $this->twig->getLoader()->addPath($path, $namespace);

        return $this;
    }

    public function setRegisterPaths(array $paths, $namespace = \Twig\Loader\FilesystemLoader::MAIN_NAMESPACE)
    {
        $this->twig->getLoader()->setPaths($paths, $namespace);

        return $this;
    }

    public function getTemplateEngine()
    {
        return $this->twig;
    }
}
