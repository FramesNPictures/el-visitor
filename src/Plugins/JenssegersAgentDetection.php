<?php

namespace FNP\ElVisitor\Plugins;

use FNP\ElVisitor\Interfaces\VisitorPlugin;
use FNP\ElVisitor\Models\Visitor;
use Jenssegers\Agent\Facades\Agent;

class JenssegersAgentDetection implements VisitorPlugin
{
    public function apply(Visitor $visitor): void
    {
        $visitor->device   = Agent::device() ?? $visitor->device;
        $visitor->browser  = Agent::browser() ?? $visitor->browser;
        $visitor->platform = Agent::platform() ?? $visitor->platform;
    }
}