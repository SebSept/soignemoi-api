<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$header = <<<'EOF'
    SoigneMoi API - Projet ECF
    
    @author Sébastien Monterisi <sebastienmonterisi@gmail.com>
    2024
    EOF;

$finder = (new Finder())
    ->ignoreDotFiles(true)
    ->ignoreVCSIgnored(true)
    ->exclude(['public', 'assets'])
    ->in(__DIR__)
;

return (new Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony' => true,
'global_namespace_import' => ['import_classes' => true, 'import_constants' => true, 'import_functions' => true]
//        'declare_strict_types' => true,
//        'header_comment' => ['header' => $header],
    ])
    ->setFinder($finder)
;
