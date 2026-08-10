<?php

use FNP\ElVisitor\Models\Visitor;
use FNP\ElVisitor\Plugins\ProvideBasicVisitorData;
use Illuminate\Http\Request;

afterEach(function (): void {
    unset($_SERVER['REMOTE_ADDR'], $_SERVER['REQUEST_URI'], $_SERVER['HTTP_USER_AGENT'], $_SERVER['HTTP_REFERER']);
});

it('sets basic visitor data', function (): void {
    $_SERVER['REMOTE_ADDR'] = '1.2.3.4';
    $_SERVER['REQUEST_URI'] = '/test-path';
    $_SERVER['HTTP_USER_AGENT'] = 'TestAgent';
    $_SERVER['HTTP_REFERER'] = 'https://example.com';

    $request = Request::create('/test-path', 'GET');
    $visitor = new Visitor();

    (new ProvideBasicVisitorData($request))->apply($visitor);

    expect($visitor->ip)->toBe('1.2.3.4')
        ->and($visitor->uri)->toBe('/test-path')
        ->and($visitor->userAgent)->toBe('TestAgent')
        ->and($visitor->referer)->toBe('https://example.com')
        ->and($visitor->visitorId)->not->toBeNull()
        ->and($visitor->requestId)->not->toBeNull()
        ->and($visitor->new)->toBeTrue();
});

it('recognises a returning visitor from the cookie', function (): void {
    $visitorId = 'existing-visitor-id';
    $request = Request::create('/', 'GET', [], [config('visitor.cookie') => $visitorId]);

    $visitor = new Visitor();
    (new ProvideBasicVisitorData($request))->apply($visitor);

    expect($visitor->visitorId)->toBe($visitorId)
        ->and($visitor->new)->toBeFalse();
});
