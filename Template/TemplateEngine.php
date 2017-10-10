<?php
namespace TelNowEdge\FreePBX\Base\Template;

use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Form\TwigRenderer;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;

class TemplateEngine implements TemplateEngineInterface
{
    /**
     * \Twig_Environment
     */
    private $twig;

    public function __construct()
    {
        $defaultFormTheme = 'form_div_layout.html.twig';

        $appVariableReflection = new \ReflectionClass('\Symfony\Bridge\Twig\AppVariable');
        $vendorTwigBridgeDir = dirname($appVariableReflection->getFileName());

        $twig = new \Twig_Environment(new \Twig_Loader_Filesystem(array(
            $vendorTwigBridgeDir.'/Resources/views/Form',
        )));

        $formEngine = new TwigRendererEngine(array($defaultFormTheme), $twig);

        $twig->addRuntimeLoader(new \Twig_FactoryRuntimeLoader(array(
            TwigRenderer::class => function () use ($formEngine, $csrfManager) {
                return new TwigRenderer($formEngine, $csrfManager);
            },
        )));

        $twig->addExtension(new FormExtension());

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
