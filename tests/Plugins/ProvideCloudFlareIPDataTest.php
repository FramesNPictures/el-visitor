<?php

namespace FNP\ElVisitor\Tests\Plugins;

use FNP\ElVisitor\Models\Visitor;
use FNP\ElVisitor\Plugins\ProvideCloudFlareIPData;
use FNP\ElVisitor\Tests\TestCase;

class ProvideCloudFlareIPDataTest extends TestCase
{
    public function test_it_extracts_cloudflare_data(): void
    {
        $_SERVER['HTTP_CF_CONNECTING_IP'] = '1.1.1.1';
        $_SERVER['HTTP_CF_IPCOUNTRY'] = 'US';
        $_SERVER['HTTP_CF_RAY'] = 'ray-id-123';

        $visitor = new Visitor;
        $plugin = new ProvideCloudFlareIPData;
        $plugin->apply($visitor);

        $this->assertEquals('1.1.1.1', $visitor->ip);
        $this->assertEquals('US', $visitor->country);
        $this->assertEquals('ray-id-123', $visitor->requestId);
    }

    public function test_it_extracts_cloudflare_forwarded_ip(): void
    {
        $_SERVER['HTTP_CF_FORWARDED_FOR'] = '2.2.2.2';

        $visitor = new Visitor;
        $plugin = new ProvideCloudFlareIPData;
        $plugin->apply($visitor);

        $this->assertEquals('2.2.2.2', $visitor->ip);
    }

    protected function tearDown(): void
    {
        unset($_SERVER['HTTP_CF_CONNECTING_IP']);
        unset($_SERVER['HTTP_CF_FORWARDED_FOR']);
        unset($_SERVER['HTTP_CF_IPCOUNTRY']);
        unset($_SERVER['HTTP_CF_RAY']);
        parent::tearDown();
    }
}
