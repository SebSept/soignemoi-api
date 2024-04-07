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
    ->withSkipPath('config/bundles.php')
    ->withImportNames(removeUnusedImports: true)
    // uncomment to reach your current PHP version
     ->withPhpSets(php83: true)
    ->withSets([\Rector\Doctrine\Set\DoctrineSetList::DOCTRINE_CODE_QUALITY])
//    ->withSkip()
    ->withRules([
//        AddVoidReturnTypeWhereNoReturnRector::class,
    ])
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
        typeDeclarations: true,
        privatization: true,
//        naming: true, // veux renomer des champs des entités doctrine.
        instanceOf: true,
        earlyReturn: true,
        strictBooleans: true
    )
    ;
