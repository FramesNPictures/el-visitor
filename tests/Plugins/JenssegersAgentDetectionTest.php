<?php

namespace FNP\ElVisitor\Tests\Plugins;

use FNP\ElVisitor\Models\Visitor;
use FNP\ElVisitor\Plugins\JenssegersAgentDetection;
use FNP\ElVisitor\Tests\TestCase;

class JenssegersAgentDetectionTest extends TestCase
{
    public function test_it_uses_jenssegers_agent_detection(): void
    {
        $visitor = new Visitor;
        $visitor->userAgent = 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_4 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0.3 Mobile/15E148 Safari/604.1';

        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'en-US,en;q=0.9';

        $plugin = new JenssegersAgentDetection;
        $plugin->apply($visitor);

        $this->assertEquals('iPhone', $visitor->device);
        $this->assertEquals('Safari', $visitor->browser);
        $this->assertEquals('iOS', $visitor->platform);
        $this->assertFalse($visitor->isRobot);
        $this->assertContains('en-us', $visitor->languages);
    }

    protected function tearDown(): void
    {
        unset($_SERVER['HTTP_ACCEPT_LANGUAGE']);
        parent::tearDown();
    }
}
