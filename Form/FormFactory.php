<?php

namespace TelNowEdge\FreePBX\Base\Form;

use Symfony\Component\Form\Extension\Csrf\CsrfExtension;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Forms;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Security\Csrf\TokenGenerator\UriSafeTokenGenerator;
use Symfony\Component\Security\Csrf\TokenStorage\SessionTokenStorage;
use Symfony\Component\Validator\Validation;

class FormFactory implements FormFactoryInterface
{
    private static $instance;

    public static function getInstance()
    {
        if (false === isset(static::$instance)) {
            static::$instance = static::create();
        }

        return static::$instance;
    }

    private static function create()
    {
        $csrfManager = self::createCsrfManager();
        $validator = self::createValidator();

        $formFactory = Forms::createFormFactoryBuilder()
            ->addExtension(new HttpFoundationExtension())
            ->addExtension(new CsrfExtension($csrfManager))
            ->addExtension(new ValidatorExtension($validator))
            ->getFormFactory();

        return $formFactory;
    }

    private static function createValidator()
    {
        return Validation::createValidator();
    }

    private static function createCsrfManager()
    {
        /**
         * migrate prevent that the session initialize by FPX conflict with Symfony components
         */
        $session = new Session();
        $session->migrate();

        $csrfGenerator = new UriSafeTokenGenerator();
        $csrfStorage = new SessionTokenStorage($session);

        return new CsrfTokenManager($csrfGenerator, $csrfStorage);
    }
}
