<?php

return [
    // Name of the visitor token cookie
    "cookie" => "APP-V",

    "ip" => [
        // IP Source Class
        'source' => \FNP\ElVisitor\Sources\IpInfo::class,
    ],
];