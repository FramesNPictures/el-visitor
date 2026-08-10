<?php

use FNP\ElVisitor\Models\Visitor;
use FNP\ElVisitor\Plugins\MobileBrowserDetection;

it('detects mobile browsers', function (?string $userAgent, ?bool $expected): void {
    $visitor = new Visitor();
    $visitor->userAgent = $userAgent;

    (new MobileBrowserDetection())->apply($visitor);

    expect($visitor->isMobile)->toBe($expected);
})->with([
    ['Mozilla/5.0 (iPhone; CPU iPhone OS 14_4 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0.3 Mobile/15E148 Safari/604.1', true],
    ['Mozilla/5.0 (Linux; Android 10; SM-G981B) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.162 Mobile Safari/537.36', true],
    ['Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36', false],
    ['Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36', false],
    [null, null],
]);
