<?php

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
