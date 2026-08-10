<?php

use FNP\ElVisitor\Models\Visitor;
use FNP\ElVisitor\Plugins\ProvideCloudFlareIPData;

afterEach(function (): void {
    unset($_SERVER['HTTP_CF_CONNECTING_IP'], $_SERVER['HTTP_CF_FORWARDED_FOR'], $_SERVER['HTTP_CF_IPCOUNTRY'], $_SERVER['HTTP_CF_RAY']);
});

it('extracts cloudflare data', function (): void {
    $_SERVER['HTTP_CF_CONNECTING_IP'] = '1.1.1.1';
    $_SERVER['HTTP_CF_IPCOUNTRY'] = 'US';
    $_SERVER['HTTP_CF_RAY'] = 'ray-id-123';

    $visitor = new Visitor();
    (new ProvideCloudFlareIPData())->apply($visitor);

    expect($visitor->ip)->toBe('1.1.1.1')
        ->and($visitor->country)->toBe('US')
        ->and($visitor->requestId)->toBe('ray-id-123');
});

it('extracts the cloudflare forwarded ip', function (): void {
    $_SERVER['HTTP_CF_FORWARDED_FOR'] = '2.2.2.2';

    $visitor = new Visitor();
    (new ProvideCloudFlareIPData())->apply($visitor);

    expect($visitor->ip)->toBe('2.2.2.2');
});
