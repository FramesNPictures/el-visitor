<?php

return [
    // Visitor Token cookie name
    "cookie" => "APP-V",

    "extensions" => [
        \FNP\ElVisitor\Extensions\CheckIpInfo::class,
        \FNP\ElVisitor\Extensions\CheckMobileBrowser::class,
        \FNP\ElVisitor\Extensions\CheckBotCrowler::class
    ],

    'services' => [
        'ip_info_token' => env('VISITOR_IP_INFO_TOKEN', null),
    ],
];