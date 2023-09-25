<?php

namespace FNP\ElVisitor\Models;

class Visitor
{
    public ?string $ip           = null;
    public ?string $requestId    = null;
    public ?bool   $new          = true;
    public ?string $token        = null;
    public ?string $userAgent    = null;
    public ?string $uri          = null;
    public ?string $referer      = null;
    public ?string $city         = null;
    public ?string $region       = null;
    public ?string $country      = null;
    public ?string $location     = null;
    public ?string $organisation = null;
    public ?string $postcode     = null;
    public ?string $timezone     = null;
    public bool    $isBot        = false;
    public bool    $isMobile     = false;
}