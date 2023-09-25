<?php

namespace FNP\ElVisitor\Plugins;

use FNP\ElVisitor\Interfaces\VisitorPlugin;
use FNP\ElVisitor\Models\Visitor;
use Ramsey\Uuid\Uuid;

class ProvideBasicVisitorData implements VisitorPlugin
{
    public function apply(Visitor $visitor): void
    {
        // Token
        $visitor->token = $_COOKIE['APP_TOKEN'] ?? Uuid::uuid4();

        // IP
        $visitor->ip = $_SERVER['REMOTE_ADDR'] ?? $visitor->ip;
        $visitor->ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $visitor->ip;

        // URI
        $visitor->uri = $_SERVER['REQUEST_URI'] ?? $visitor->uri;

        // User Agent
        $visitor->userAgent = $_SERVER['HTTP_USER_AGENT'] ?? $visitor->userAgent;

        // Referer
        $visitor->referer = $_SERVER['HTTP_REFERER'] ?? $visitor->referer;
    }
}