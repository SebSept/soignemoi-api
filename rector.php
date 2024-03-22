<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;

return static function (RectorConfig $rectorConfig): void {

    $rectorConfig->importNames();

    $rectorConfig->paths([
        __DIR__ . '/src',
        __DIR__ . '/rector.php',
    ]);

//    $rectorConfig->phpVersion(\Rector\Core\ValueObject\PhpVersion::PHP_74);
//    $rectorConfig->containerCacheDirectory();
//    $rectorConfig->rule(ReadOnlyClassRector::class);

//     define sets of rules
    $rectorConfig->sets([
        LevelSetList::UP_TO_PHP_82,
//        DowngradeLevelSetList::DOWN_TO_PHP_74
        SetList::TYPE_DECLARATION,
        SetList::CODING_STYLE,
        SetList::INSTANCEOF,
        SetList::CODE_QUALITY,
    ]);
};
