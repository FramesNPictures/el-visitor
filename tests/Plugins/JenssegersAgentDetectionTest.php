<?php

use FNP\ElVisitor\Models\Visitor;
use FNP\ElVisitor\Plugins\JenssegersAgentDetection;

afterEach(function (): void {
    unset($_SERVER['HTTP_ACCEPT_LANGUAGE']);
});

it('uses jenssegers agent detection', function (): void {
    $visitor = new Visitor();
    $visitor->userAgent = 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_4 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0.3 Mobile/15E148 Safari/604.1';

    $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'en-US,en;q=0.9';

    (new JenssegersAgentDetection())->apply($visitor);

    expect($visitor->device)->toBe('iPhone')
        ->and($visitor->browser)->toBe('Safari')
        ->and($visitor->platform)->toBe('iOS')
        ->and($visitor->isRobot)->toBeFalse()
        ->and($visitor->languages)->toContain('en-us');
});
