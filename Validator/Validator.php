<?php

namespace TelNowEdge\FreePBX\Base\Validator;

use Symfony\Component\Validator\Validation;

class Validator
{
    private static $instance;

    public static function getInstance($c)
    {
        if (false === isset(static::$instance)) {
            static::$instance = static::create($c);
        }

        return static::$instance;
    }

    private static function create($c)
    {
        $loader = include(__DIR__ . "/../../../autoload.php");

        \Doctrine\Common\Annotations\AnnotationRegistry::registerLoader(
            array($loader, 'loadClass')
        );

        return Validation::createValidatorBuilder()
            ->enableAnnotationMapping()
            ->setConstraintValidatorFactory($c->get('validator.validator_factory'))
            ->getValidator();
        ;
    }
}
