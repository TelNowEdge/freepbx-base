<?php

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PHP82Migration' => true,
        '@Symfony' => true,
        '@Symfony:risky' => true,
        'array_syntax' => true,
        'combine_consecutive_unsets' => true,
        'no_useless_else' => true,
        'no_useless_return' => true,
        'ordered_class_elements' => true,
        'ordered_imports' => true,
        'php_unit_strict' => true,
        'strict_comparison' => true,
        'strict_param' => true,
        '@PhpCsFixer' => true,
        '@PhpCsFixer:risky' => true,
    ])
    ->setFinder(PhpCsFixer\Finder::create()
        ->exclude('vendor')
        ->in(__DIR__)
    );

/*
This document has been generated with
https://mlocati.github.io/php-cs-fixer-configurator/
you can change this configuration by importing this YAML code:

fixerSets:
  - '@Symfony'
  - '@Symfony:risky'
risky: true
fixers:
  array_syntax: true
  combine_consecutive_unsets: true
  no_useless_else: true
  no_useless_return: true
  ordered_class_elements: true
  ordered_imports: true
  php_unit_strict: true
  strict_comparison: true
  strict_param: true

*/
