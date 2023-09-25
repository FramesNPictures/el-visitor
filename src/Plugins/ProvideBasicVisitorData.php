<?php

namespace FNP\ElVisitor\Plugins;

use FNP\ElVisitor\Interfaces\VisitorPlugin;
use FNP\ElVisitor\Models\Visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Ramsey\Uuid\Uuid;

class ProvideBasicVisitorData implements VisitorPlugin
{
    private Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply(Visitor $visitor): void
    {
        // Token
        $visitor->token = $this->request->cookie(config('visitor.cookie'), Uuid::uuid4()->toString());

        // IP
        $visitor->ip = $_SERVER['REMOTE_ADDR'] ?? $visitor->ip;
        $visitor->ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $visitor->ip;

        // URI
        $visitor->uri = $_SERVER['REQUEST_URI'] ?? $visitor->uri;

        // User Agent
        $visitor->userAgent = $_SERVER['HTTP_USER_AGENT'] ?? $visitor->userAgent;

        // Referer
        $visitor->referer = $_SERVER['HTTP_REFERER'] ?? $visitor->referer;

        // RequestId
        $visitor->requestId = Uuid::uuid4()->toString();

        // Store token
        Cookie::queue(config('visitor.cookie'), $visitor->token, 365 * 24 * 60);
    }
}