<?php

namespace FNP\ElVisitor\Tests\Plugins;

use FNP\ElVisitor\Models\Visitor;
use FNP\ElVisitor\Plugins\BotCrowlerDetection;
use FNP\ElVisitor\Tests\TestCase;

class BotCrowlerDetectionTest extends TestCase
{
    public static function botUserAgents()
    {
        return [
            ['Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)', true],
            ['Mozilla/5.0 (compatible; Bingbot/2.0; +http://www.bing.com/bingbot.htm)', true],
            ['Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36', false],
            ['curl/7.64.1', true],
            [null, null],
        ];
    }

    /**
     * @dataProvider botUserAgents
     */
    public function test_it_detects_bots($userAgent, $expected): void
    {
        $visitor = new Visitor();
        $visitor->userAgent = $userAgent;

        $plugin = new BotCrowlerDetection();
        $plugin->apply($visitor);

        $this->assertEquals($expected, $visitor->isRobot);
    }
}
