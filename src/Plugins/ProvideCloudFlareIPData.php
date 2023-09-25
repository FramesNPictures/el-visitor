<?php

namespace FNP\ElVisitor\Plugins;

use FNP\ElVisitor\Interfaces\VisitorPlugin;
use FNP\ElVisitor\Models\Visitor;

class ProvideCloudFlareIPData implements VisitorPlugin
{
    public function apply(Visitor $visitor): void
    {
        // IP
        $visitor->ip = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $visitor->ip;
        $visitor->ip = $_SERVER['HTTP_CF_FORWARDED_FOR'] ?? $visitor->ip;

        // Country
        $visitor->country = $_SERVER['HTTP_CF_IPCOUNTRY'] ?? $visitor->country;

        // RequestID
        $visitor->requestId = $_SERVER['HTTP_CF_RAY'] ?? $visitor->requestId;
    }
}