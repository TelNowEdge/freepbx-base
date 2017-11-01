<?php

namespace TelNowEdge\FreePBX\Base\Form;

use Symfony\Component\Form\Extension\Csrf\CsrfExtension;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Forms;

class FormFactory implements FormFactoryInterface
{
    private static $instance;

    public static function getInstance(\Symfony\Component\Security\Csrf\CsrfTokenManager $csrfManager)
    {
        if (false === isset(static::$instance)) {
            static::$instance = static::create($csrfManager);
        }

        return static::$instance;
    }

    private static function create(\Symfony\Component\Security\Csrf\CsrfTokenManager $csrfManager)
    {
        $formFactory = Forms::createFormFactoryBuilder()
            ->addExtension(new HttpFoundationExtension())
            ->addExtension(new CsrfExtension($csrfManager))
            ->addExtension(new ValidatorExtension(
                \TelNowEdge\FreePBX\Base\Validator\Validator::getInstance('toto')
            ))
            ->getFormFactory();

        return $formFactory;
    }
}
