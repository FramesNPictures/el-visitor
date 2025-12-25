<?php

namespace FNP\ElVisitor\Tests\Plugins;

use FNP\ElVisitor\Models\Visitor;
use FNP\ElVisitor\Plugins\Services\DataByAbuseIPDB;
use FNP\ElVisitor\Tests\TestCase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Mockery;

class DataByAbuseIPDBTest extends TestCase
{
    public function test_it_does_nothing_if_token_is_missing(): void
    {
        $visitor = new Visitor();
        $visitor->ip = '1.2.3.4';

        $plugin = new DataByAbuseIPDB(null);
        $plugin->apply($visitor);

        $this->assertEquals(0, $visitor->abuseScore);
        Http::assertNothingSent();
    }

    public function test_it_does_nothing_for_non_routable_ips(): void
    {
        $visitor = new Visitor();
        $plugin = new DataByAbuseIPDB('some-token');

        $ips = ['10.0.0.1', '192.168.1.1', '172.16.0.1'];

        foreach ($ips as $ip) {
            $visitor->ip = $ip;
            $plugin->apply($visitor);
            $this->assertEquals(0, $visitor->abuseScore);
        }

        Http::assertNothingSent();
    }

    public function test_it_fetches_and_applies_data_from_abuseipdb(): void
    {
        $ip = '1.2.3.4';
        $visitor = new Visitor();
        $visitor->ip = $ip;

        $mockData = [
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

        Http::fake([
            'api.abuseipdb.com/api/v2/check*' => Http::response($mockData, 200),
        ]);

        $plugin = new DataByAbuseIPDB('test-token');
        $plugin->apply($visitor);

        $this->assertEquals('US', $visitor->country);
        $this->assertEquals('Google LLC', $visitor->organisation);
        $this->assertEquals(10, $visitor->abuseScore);
        $this->assertEquals(5, $visitor->abuseReports);
        $this->assertFalse($visitor->isTor);
        $this->assertEquals('google.com', $visitor->extra['domain']);
        $this->assertEquals(['dns.google'], $visitor->extra['hostnames']);

        Http::assertSent(fn ($request): bool => $request->url() === 'https://api.abuseipdb.com/api/v2/check?ipAddress=' . $ip &&
               $request->hasHeader('Key', 'test-token') &&
               $request->hasHeader('Accept', 'application/json'));
    }

    public function test_it_caches_the_api_response(): void
    {
        $ip = '1.2.3.4';
        $visitor = new Visitor();
        $visitor->ip = $ip;

        $mockData = [
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

        Http::fake([
            'api.abuseipdb.com/api/v2/check*' => Http::response($mockData, 200),
        ]);

        $plugin = new DataByAbuseIPDB('test-token');

        // First call
        $plugin->apply($visitor);

        // Second call (should use cache)
        $plugin->apply($visitor);

        Http::assertSentCount(1);
    }

    public function test_it_handles_exceptions_gracefully(): void
    {
        $visitor = new Visitor();
        $visitor->ip = '1.2.3.4';

        Http::fake([
            'api.abuseipdb.com/*' => Http::response('Error', 500),
        ]);

        Log::shouldReceive('warning')
            ->once()
            ->with('Problem obtaining AbuseIPDB', Mockery::any());

        $plugin = new DataByAbuseIPDB('test-token');
        $plugin->apply($visitor);

        $this->assertEquals(0, $visitor->abuseScore);
    }
}
