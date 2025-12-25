<?php

namespace FNP\ElVisitor\Tests\Plugins;

use FNP\ElVisitor\Models\Visitor;
use FNP\ElVisitor\Plugins\ClientHintsDetection;
use FNP\ElVisitor\Tests\TestCase;

class ClientHintsDetectionTest extends TestCase
{
    protected function tearDown(): void
    {
        unset($_SERVER['HTTP_SEC_CH_UA'], $_SERVER['HTTP_SEC_CH_UA_PLATFORM'], $_SERVER['HTTP_SEC_CH_UA_MOBILE']);

        parent::tearDown();
    }

    public function test_extracts_platform_from_client_hints(): void
    {
        $_SERVER['HTTP_SEC_CH_UA_PLATFORM'] = '"Windows"';

        $visitor = new Visitor();
        $plugin = new ClientHintsDetection();
        $plugin->apply($visitor);

        $this->assertEquals('Windows', $visitor->platform);
    }

    public function test_extracts_mobile_status_from_client_hints(): void
    {
        $_SERVER['HTTP_SEC_CH_UA_MOBILE'] = '?1';

        $visitor = new Visitor();
        $plugin = new ClientHintsDetection();
        $plugin->apply($visitor);

        $this->assertTrue($visitor->isMobile);

        $_SERVER['HTTP_SEC_CH_UA_MOBILE'] = '?0';
        $plugin->apply($visitor);
        $this->assertFalse($visitor->isMobile);
    }
}
