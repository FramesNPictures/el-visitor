<?php

namespace FNP\ElVisitor\Tests\Plugins;

use FNP\ElVisitor\Models\Visitor;
use FNP\ElVisitor\Plugins\Services\DataByLocalDatabase;
use FNP\ElVisitor\Tests\TestCase;

class DataByLocalDatabaseTest extends TestCase
{
    public function test_it_resolves_data_from_local_database_ipv4(): void
    {
        $visitor = new Visitor;
        $visitor->ip = '1.1.1.1';
        $visitor->ipVersion = 4;

        $plugin = new DataByLocalDatabase;
        $plugin->apply($visitor);

        // Based on 1.1.1.1, we expect Cloudflare
        $this->assertEquals(13335, $visitor->providerId);
        $this->assertEquals('Cloudflare, Inc.', $visitor->providerName);
        $this->assertEquals('AU', $visitor->country);
    }

    public function test_it_resolves_data_from_local_database_ipv6(): void
    {
        $visitor = new Visitor;
        $visitor->ip = '2606:4700:4700::1111';
        $visitor->ipVersion = 6;

        $plugin = new DataByLocalDatabase;
        $plugin->apply($visitor);

        $this->assertEquals(13335, $visitor->providerId);
        $this->assertEquals('Cloudflare, Inc.', $visitor->providerName);
        $this->assertEquals('CA', $visitor->country);
    }
}
