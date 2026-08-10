<?php

use FNP\ElVisitor\Models\Visitor;
use FNP\ElVisitor\Plugins\RecogniseIPVersion;

it('recognises the ip version', function (?string $ip, ?int $expectedVersion): void {
    $visitor = new Visitor();
    $visitor->ip = $ip;

    (new RecogniseIPVersion())->apply($visitor);

    expect($visitor->ipVersion)->toBe($expectedVersion);
})->with([
    ['127.0.0.1', 4],
    ['8.8.8.8', 4],
    ['2001:4860:4860::8888', 6],
    ['::1', 6],
    [null, null],
]);
