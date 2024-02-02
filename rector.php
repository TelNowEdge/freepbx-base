<?php

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\SetList;
use Rector\TypeDeclaration\Rector\Property\TypedPropertyFromStrictConstructorRector;

return function (RectorConfig $rectorConfig) {
    $rectorConfig->skip([
        __DIR__ . '/vendor',
        __DIR__ . '/../../libraries',
    ]);

    // register single rule
    $rectorConfig->rule(TypedPropertyFromStrictConstructorRector::class);

    // here we can define, what sets of rules will be applied
    // tip: use "SetList" class to autocomplete sets with your IDE
    $rectorConfig->sets([
        SetList::CODE_QUALITY,
        SetList::PHP_82,
        SetList::TYPE_DECLARATION,
//        SymfonySetList::SYMFONY_64,
//        SymfonySetList::SYMFONY_CODE_QUALITY,
//        SymfonySetList::SYMFONY_CONSTRUCTOR_INJECTION,
    ]);
};
