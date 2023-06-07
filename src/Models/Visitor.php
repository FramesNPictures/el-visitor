<?php

namespace FNP\ElVisitor\Models;

class Visitor
{
    public ?string $ip             = null;
    public ?bool   $new            = true;
    public ?string $token          = null;
    public ?string $userAgent      = null;
    public ?string $uri            = null;
    public ?string $browserName    = null;
    public ?string $browserVersion = null;
    public ?string $browserEngine  = null;
    public ?string $osName         = null;
    public ?string $osVersion      = null;
    public ?string $osAlias        = null;
    public ?string $osFamily       = null;
    public ?string $deviceModel    = null;
    public ?string $deviceMake     = null;
    public ?string $referer        = null;
    public ?string $city           = null;
    public ?string $region         = null;
    public ?string $country        = null;
    public ?string $location       = null;
    public ?string $organisation   = null;
    public ?string $postcode       = null;
    public ?string $timezone       = null;
}