<?php

use FNP\ElVisitor\Models\Visitor;
use FNP\ElVisitor\Services\VisitorService;
use FNP\ElVisitor\Tests\IntegrationTestCase;

uses(IntegrationTestCase::class);

it('resolves a visitor for the incoming ip address', function (string $ip, int $expectedVersion): void {
    $_SERVER['REMOTE_ADDR'] = $ip;

    $visitor = app(VisitorService::class)->visitor();

    expect($visitor)->toBeInstanceOf(Visitor::class)
        ->and($visitor->ip)->toBe($ip)
        ->and($visitor->ipVersion)->toBe($expectedVersion);
})->with([
    ['132.198.200.196', 4],
    ['23.228.130.134', 4],
    ['62.210.243.153', 4],
    ['2607:fb90:e33c:c367:8c19:2ff:fe47:d1bb', 6],
    ['2603:7080:b700:9f4c:fd84:4e0e:3f68:4c7c', 6],
    ['2.24.127.161', 4],
]);
