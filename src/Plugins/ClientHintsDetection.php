<?php

namespace FNP\ElVisitor\Plugins;

use FNP\ElVisitor\Interfaces\VisitorPlugin;
use FNP\ElVisitor\Models\Visitor;

class ClientHintsDetection implements VisitorPlugin
{
    public function apply(Visitor $visitor): void
    {
        // Platform
        if (isset($_SERVER['HTTP_SEC_CH_UA_PLATFORM'])) {
            $visitor->platform = str_replace('"', '', $_SERVER['HTTP_SEC_CH_UA_PLATFORM']);
        }

        // Mobile
        if (isset($_SERVER['HTTP_SEC_CH_UA_MOBILE'])) {
            $visitor->isMobile = $_SERVER['HTTP_SEC_CH_UA_MOBILE'] === '?1';
        }
    }
}
