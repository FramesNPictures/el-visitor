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

        // IpInfo.io is not free anymore
        //\FNP\ElVisitor\Plugins\LocationProvidedByIpinfoIO::class=>[
        //    'token' => env('VISITOR_IP_INFO_TOKEN', null),
        //],
    ],
];