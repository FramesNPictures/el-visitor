<?php

namespace FNP\ElVisitor\Models;

class Visitor
{
    public ?string $requestId    = null;
    public ?string $visitorId    = null;
    public ?string $ip           = null;
    public ?bool   $new          = true;
    public ?string $userAgent    = null;
    public ?string $browser      = null;
    public ?string $device       = null;
    public ?string $platform     = null;
    public array   $languages    = [];
    public ?string $uri          = null;
    public ?string $referer      = null;
    public ?string $city         = null;
    public ?string $region       = null;
    public ?string $country      = null;
    public ?string $location     = null;
    public ?string $organisation = null;
    public ?string $postcode     = null;
    public ?string $timezone     = null;
    public bool    $isRobot      = false;
    public bool    $isMobile     = false;
    public array   $extra        = [];
}