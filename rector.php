<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Class_\InlineConstructorDefaultToPropertyRector;
use Rector\Config\RectorConfig;
use Rector\Doctrine\Set\DoctrineSetList;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\Symfony\Set\SensiolabsSetList;
use Rector\Symfony\Set\SymfonySetList;

return static function (RectorConfig $rectorConfig) : void {
    $rectorConfig->paths([
        __DIR__ . '/BlogBundle',
        __DIR__ . '/DublinCoreBundle',
        __DIR__ . '/EditorBundle',
        __DIR__ . '/FeedbackBundle',
        __DIR__ . '/MakerBundle',
        __DIR__ . '/MediaBundle',
        __DIR__ . '/SolrBundle',
        __DIR__ . '/UserBundle',
        __DIR__ . '/UtilBundle',
    ]);

    // register a single rule
    $rectorConfig->rule(InlineConstructorDefaultToPropertyRector::class);

    $rectorConfig->sets([
        // SetList::CODE_QUALITY,
        // LevelSetList::UP_TO_PHP_82,
        DoctrineSetList::ANNOTATIONS_TO_ATTRIBUTES,
        SymfonySetList::ANNOTATIONS_TO_ATTRIBUTES,
        SensiolabsSetList::FRAMEWORK_EXTRA_61,
    ]);
};
