<?php

namespace FNP\ElVisitor\Tests\Plugins;

use FNP\ElVisitor\Models\Visitor;
use FNP\ElVisitor\Plugins\MobileBrowserDetection;
use FNP\ElVisitor\Tests\TestCase;

class MobileBrowserDetectionTest extends TestCase
{
    public static function mobileUserAgents()
    {
        return [
            ['Mozilla/5.0 (iPhone; CPU iPhone OS 14_4 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0.3 Mobile/15E148 Safari/604.1', true],
            ['Mozilla/5.0 (Linux; Android 10; SM-G981B) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.162 Mobile Safari/537.36', true],
            ['Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36', false],
            ['Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36', false],
            [null, null],
        ];
    }

    /**
     * @dataProvider mobileUserAgents
     */
    public function test_it_detects_mobile_browsers($userAgent, $expected): void
    {
        $visitor = new Visitor();
        $visitor->userAgent = $userAgent;

        $plugin = new MobileBrowserDetection();
        $plugin->apply($visitor);

        $this->assertEquals($expected, $visitor->isMobile);
    }
}
