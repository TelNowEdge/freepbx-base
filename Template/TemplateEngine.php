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

use ReflectionClass;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Component\Form\FormRenderer;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\RuntimeLoader\FactoryRuntimeLoader;
use Twig\TwigFilter;

class TemplateEngine implements TemplateEngineInterface
{
    /**
     * \Twig_Environment.
     */
    private Environment $twig;

    public function __construct(CsrfTokenManager $csrfManager)
    {
        $defaultFormTheme = 'freepbx_layout_page.html.twig';

        $appVariableReflection = new ReflectionClass('\Symfony\Bridge\Twig\AppVariable');
        $vendorTwigBridgeDir = dirname($appVariableReflection->getFileName());

        $twig = new Environment(new FilesystemLoader([
            $vendorTwigBridgeDir . '/Resources/views/Form',
            __DIR__ . '/../Resources/views/Form',
        ]), [// 'cache' => sprintf('%s/../../../../../../assets/cache/twig/', __DIR__),
        ]);

        $twig->getLoader()->addPath(__DIR__ . '/../Resources/views', 'telnowedge');

        $formEngine = new TwigRendererEngine([$defaultFormTheme], $twig);

        $twig->addRuntimeLoader(new FactoryRuntimeLoader([
            FormRenderer::class => function () use ($formEngine, $csrfManager): \Symfony\Component\Form\FormRenderer {
                return new FormRenderer($formEngine, $csrfManager);
            },
        ]));

        $twig->addExtension(new FormExtension());

        $filter = new TwigFilter('fpbxtrans', function ($string): string {
            return _($string);
        });
        $twig->addFilter($filter);

        $filter = new TwigFilter('trans', function ($string): string {
            return _($string);
        });
        $twig->addFilter($filter);

        $this->twig = $twig;
    }

    public function addRegisterPath(string|null $path, $namespace = FilesystemLoader::MAIN_NAMESPACE): static
    {
        if ($path === null || $path === '' || $path === '0' || empty($namespace)) {
            return $this;
        }

        $paths = $this->twig->getLoader()->getPaths($namespace);

        if (in_array($path, $paths, true)) {
            return $this;
        }

        $this->twig->getLoader()->addPath($path, $namespace);

        return $this;
    }

    public function setRegisterPaths(array $paths, $namespace = FilesystemLoader::MAIN_NAMESPACE): static
    {
        $this->twig->getLoader()->setPaths($paths, $namespace);

        return $this;
    }

    public function getTemplateEngine(): Environment
    {
        return $this->twig;
    }
}
