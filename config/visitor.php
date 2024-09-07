<?php

return [
    // Visitor Token cookie name
    "cookie" => "APP-V",

    "plugins" => [
        \FNP\ElVisitor\Plugins\ProvideBasicVisitorData::class,
        \FNP\ElVisitor\Plugins\ProvideCloudFlareIPData::class,
        \FNP\ElVisitor\Plugins\MobileBrowserDetection::class,
        \FNP\ElVisitor\Plugins\BotCrowlerDetection::class,
        \FNP\ElVisitor\Plugins\JenssegersAgentDetection::class,
        \FNP\ElVisitor\Plugins\RecogniseIPVersion::class,
        \FNP\ElVisitor\Plugins\Services\DataByLocalDatabase::class,
    ],
];