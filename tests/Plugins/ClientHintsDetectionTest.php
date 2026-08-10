<?php

use FNP\ElVisitor\Models\Visitor;
use FNP\ElVisitor\Plugins\ClientHintsDetection;

afterEach(function (): void {
    unset($_SERVER['HTTP_SEC_CH_UA'], $_SERVER['HTTP_SEC_CH_UA_PLATFORM'], $_SERVER['HTTP_SEC_CH_UA_MOBILE']);
});

it('extracts the platform from client hints', function (): void {
    $_SERVER['HTTP_SEC_CH_UA_PLATFORM'] = '"Windows"';

    $visitor = new Visitor();
    (new ClientHintsDetection())->apply($visitor);

    expect($visitor->platform)->toBe('Windows');
});

it('extracts the mobile status from client hints', function (): void {
    $_SERVER['HTTP_SEC_CH_UA_MOBILE'] = '?1';

    $visitor = new Visitor();
    $plugin = new ClientHintsDetection();
    $plugin->apply($visitor);

    expect($visitor->isMobile)->toBeTrue();

    $_SERVER['HTTP_SEC_CH_UA_MOBILE'] = '?0';
    $plugin->apply($visitor);

    expect($visitor->isMobile)->toBeFalse();
});
