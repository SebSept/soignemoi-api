<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\TypeDeclaration\Rector\ClassMethod\AddVoidReturnTypeWhereNoReturnRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/config',
        __DIR__ . '/public',
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    // uncomment to reach your current PHP version
     ->withPhpSets(php83: true)
//    ->withSkip()
    ->withRules([
//        AddVoidReturnTypeWhereNoReturnRector::class,
    ])
    ->withPreparedSets(
        deadCode: true,
//        codeQuality: true,
//        codingStyle: true,
//        typeDeclarations: true,
//        privatization: true,
//        naming: true,
//        instanceOf: true,
//        earlyReturn: true,
//        strictBooleans: true
    )
//    ->withTypeCoverageLevel(37)
    ;
