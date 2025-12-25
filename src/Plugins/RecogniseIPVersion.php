<?php

namespace FNP\ElVisitor\Plugins;

use FNP\ElVisitor\Interfaces\VisitorPlugin;
use FNP\ElVisitor\Models\Visitor;

class RecogniseIPVersion implements VisitorPlugin
{
    public function apply(Visitor $visitor): void
    {
        if ( ! is_null($visitor->ip)) {
            $visitor->ipVersion = str_contains($visitor->ip, ':') ? 6 : 4;
        }
    }
}
