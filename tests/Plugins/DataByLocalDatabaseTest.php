<?php

use FNP\ElVisitor\Models\Visitor;
use FNP\ElVisitor\Plugins\Services\DataByLocalDatabase;

// The MMDB databases are not committed - they are downloaded on demand. Skip rather
// than fail when they are missing, so a fresh checkout still has a green suite.
$databasesMissing = fn (): bool => ! file_exists(__DIR__ . '/../../data/asn-ipv4.mmdb');

$skipReason = 'Local IP databases are not present. Run: php artisan app:visitor:update';

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
})->skip($databasesMissing, $skipReason);

it('resolves data from the local database for ipv6', function (): void {
    $visitor = new Visitor();
    $visitor->ip = '2606:4700:4700::1111';
    $visitor->ipVersion = 6;

    (new DataByLocalDatabase())->apply($visitor);

    expect($visitor->providerId)->toBe(13335)
        ->and($visitor->providerName)->toBe('Cloudflare, Inc.')
        ->and($visitor->country)->toBe('CA')
        ->and($visitor->region)->toBe('Quebec');
})->skip($databasesMissing, $skipReason);
