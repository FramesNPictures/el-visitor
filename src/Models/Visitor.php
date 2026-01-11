<?php

namespace FNP\ElVisitor\Models;

class Visitor
{
    public ?string $requestId = null;

    public ?string $visitorId = null;

    public ?string $ip = null;

    public ?int $ipVersion = null;

    public ?bool $new = true;

    public ?string $userAgent = null;

    public ?string $browser = null;

    public ?string $device = null;

    public ?string $platform = null;

    public array $languages = [];

    public ?string $uri = null;

    public ?string $referer = null;

    public ?string $city = null;

    public ?string $region = null;

    public ?string $country = null;

    public ?string $location = null;

    public ?string $organisation = null;

    public ?int $providerId = null;

    public ?string $providerName = null;

    public ?string $providerType = null;

    public ?string $postcode = null;

    public ?string $timezone = null;

    public ?bool $isRobot = null;

    public ?bool $isMobile = null;

    public ?bool $isTor = null;

    public ?bool $isDataCenter = null;

    public int $abuseScore = 0;

    public int $abuseReports = 0;

    public array $extra = [];
}
