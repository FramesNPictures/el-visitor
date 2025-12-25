<?php

namespace FNP\ElVisitor\Tests\Plugins;

use FNP\ElVisitor\Models\Visitor;
use FNP\ElVisitor\Plugins\RecogniseIPVersion;
use FNP\ElVisitor\Tests\TestCase;

class RecogniseIPVersionTest extends TestCase
{
    /**
     * @dataProvider ipVersions
     */
    public function test_it_recognizes_ip_version($ip, $expectedVersion): void
    {
        $visitor = new Visitor;
        $visitor->ip = $ip;

        $plugin = new RecogniseIPVersion;
        $plugin->apply($visitor);

        $this->assertEquals($expectedVersion, $visitor->ipVersion);
    }

    public static function ipVersions()
    {
        return [
            ['127.0.0.1', 4],
            ['8.8.8.8', 4],
            ['2001:4860:4860::8888', 6],
            ['::1', 6],
            [null, null],
        ];
    }
}
