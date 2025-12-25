<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/src',
        __DIR__.'/tests',
    ])
    // uncomment to reach your current PHP version
    // ->withPhpSets()
    ->withPreparedSets(
        privatization: true,
        instanceOf: true,
        earlyReturn: true,
        carbon: true,
    )
    ->withDeadCodeLevel(0)
    ->withCodeQualityLevel(9)
    ->withTypeCoverageLevel(9)
    ->withIndent()
    ->withPhpSets(
        php82: true,
    );
