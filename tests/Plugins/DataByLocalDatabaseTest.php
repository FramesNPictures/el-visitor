<?php

use FNP\ElVisitor\Models\Visitor;
use FNP\ElVisitor\Plugins\Services\DataByLocalDatabase;

it('resolves data from the local database for ipv4', function (): void {
    $visitor = new Visitor();
    $visitor->ip = '1.1.1.1';
    $visitor->ipVersion = 4;

    (new DataByLocalDatabase())->apply($visitor);

    // Based on 1.1.1.1, we expect Cloudflare
    expect($visitor->providerId)->toBe(13335)
        ->and($visitor->providerName)->toBe('Cloudflare, Inc.')
        ->and($visitor->country)->toBe('AU')
        ->and($visitor->region)->toBe('New South Wales');
});

it('resolves data from the local database for ipv6', function (): void {
    $visitor = new Visitor();
    $visitor->ip = '2606:4700:4700::1111';
    $visitor->ipVersion = 6;

    (new DataByLocalDatabase())->apply($visitor);

    expect($visitor->providerId)->toBe(13335)
        ->and($visitor->providerName)->toBe('Cloudflare, Inc.')
        ->and($visitor->country)->toBe('CA')
        ->and($visitor->region)->toBe('Quebec');
});
