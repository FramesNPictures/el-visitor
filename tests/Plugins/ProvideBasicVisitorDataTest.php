<?php

namespace FNP\ElVisitor\Tests\Plugins;

use FNP\ElVisitor\Models\Visitor;
use FNP\ElVisitor\Plugins\ProvideBasicVisitorData;
use FNP\ElVisitor\Tests\TestCase;
use Illuminate\Http\Request;

class ProvideBasicVisitorDataTest extends TestCase
{
    public function test_it_sets_basic_visitor_data(): void
    {
        $_SERVER['REMOTE_ADDR'] = '1.2.3.4';
        $_SERVER['REQUEST_URI'] = '/test-path';
        $_SERVER['HTTP_USER_AGENT'] = 'TestAgent';
        $_SERVER['HTTP_REFERER'] = 'https://example.com';

        $request = Request::create('/test-path', 'GET');
        $visitor = new Visitor;
        $plugin = new ProvideBasicVisitorData($request);

        $plugin->apply($visitor);

        $this->assertEquals('1.2.3.4', $visitor->ip);
        $this->assertEquals('/test-path', $visitor->uri);
        $this->assertEquals('TestAgent', $visitor->userAgent);
        $this->assertEquals('https://example.com', $visitor->referer);
        $this->assertNotNull($visitor->visitorId);
        $this->assertNotNull($visitor->requestId);
        $this->assertTrue($visitor->new);
    }

    public function test_it_recognizes_returning_visitor_from_cookie(): void
    {
        $visitorId = 'existing-visitor-id';
        $request = Request::create('/', 'GET', [], [config('visitor.cookie') => $visitorId]);

        $visitor = new Visitor;
        $plugin = new ProvideBasicVisitorData($request);
        $plugin->apply($visitor);

        $this->assertEquals($visitorId, $visitor->visitorId);
        $this->assertFalse($visitor->new);
    }

    protected function tearDown(): void
    {
        unset($_SERVER['REMOTE_ADDR']);
        unset($_SERVER['REQUEST_URI']);
        unset($_SERVER['HTTP_USER_AGENT']);
        unset($_SERVER['HTTP_REFERER']);
        parent::tearDown();
    }
}
