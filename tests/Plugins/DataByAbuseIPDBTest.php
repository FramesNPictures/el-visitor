<?php

use FNP\ElVisitor\Models\Visitor;
use FNP\ElVisitor\Plugins\Services\DataByAbuseIPDB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

function abuseIpdbResponse(): array
{
    return [
        'data' => [
            'countryCode' => 'US',
            'isp' => 'Google LLC',
            'abuseConfidenceScore' => 10,
            'totalReports' => 5,
            'isTor' => false,
            'domain' => 'google.com',
            'hostnames' => ['dns.google'],
        ],
    ];
}

it('does nothing if the token is missing', function (): void {
    $visitor = new Visitor();
    $visitor->ip = '1.2.3.4';

    (new DataByAbuseIPDB(null))->apply($visitor);

    expect($visitor->abuseScore)->toBe(0);
    Http::assertNothingSent();
});

it('does nothing for non routable ips', function (): void {
    $visitor = new Visitor();
    $plugin = new DataByAbuseIPDB('some-token');

    foreach (['10.0.0.1', '192.168.1.1', '172.16.0.1'] as $ip) {
        $visitor->ip = $ip;
        $plugin->apply($visitor);

        expect($visitor->abuseScore)->toBe(0);
    }

    Http::assertNothingSent();
});

it('fetches and applies data from abuseipdb', function (): void {
    $ip = '1.2.3.4';
    $visitor = new Visitor();
    $visitor->ip = $ip;

    Http::fake([
        'api.abuseipdb.com/api/v2/check*' => Http::response(abuseIpdbResponse(), 200),
    ]);

    (new DataByAbuseIPDB('test-token'))->apply($visitor);

    expect($visitor->country)->toBe('US')
        ->and($visitor->organisation)->toBe('Google LLC')
        ->and($visitor->abuseScore)->toBe(10)
        ->and($visitor->abuseReports)->toBe(5)
        ->and($visitor->isTor)->toBeFalse()
        ->and($visitor->extra['domain'])->toBe('google.com')
        ->and($visitor->extra['hostnames'])->toBe(['dns.google']);

    Http::assertSent(fn ($request): bool => $request->url() === 'https://api.abuseipdb.com/api/v2/check?ipAddress=' . $ip &&
           $request->hasHeader('Key', 'test-token') &&
           $request->hasHeader('Accept', 'application/json'));
});

it('caches the api response', function (): void {
    $visitor = new Visitor();
    $visitor->ip = '1.2.3.4';

    Http::fake([
        'api.abuseipdb.com/api/v2/check*' => Http::response(abuseIpdbResponse(), 200),
    ]);

    $plugin = new DataByAbuseIPDB('test-token');

    // First call
    $plugin->apply($visitor);

    // Second call (should use cache)
    $plugin->apply($visitor);

    Http::assertSentCount(1);
});

it('handles exceptions gracefully', function (): void {
    $visitor = new Visitor();
    $visitor->ip = '1.2.3.4';

    Http::fake([
        'api.abuseipdb.com/*' => Http::response('Error', 500),
    ]);

    Log::shouldReceive('warning')
        ->once()
        ->with('Problem obtaining AbuseIPDB', Mockery::any());

    (new DataByAbuseIPDB('test-token'))->apply($visitor);

    expect($visitor->abuseScore)->toBe(0);
});
