<?php

namespace FNP\ElVisitor\Plugins;

use FNP\ElVisitor\Interfaces\VisitorPlugin;
use FNP\ElVisitor\Models\Visitor;
use Jenssegers\Agent\Agent;

class JenssegersAgentDetection implements VisitorPlugin
{
    public function apply(Visitor $visitor): void
    {
        $agent = new Agent();
        $agent->setUserAgent($visitor->userAgent);
        $agent->setHttpHeaders($_SERVER);
        $visitor->device    = $agent->device() ?? $visitor->device;
        $visitor->browser   = $agent->browser() ?? $visitor->browser;
        $visitor->platform  = $agent->platform() ?? $visitor->platform;
        $visitor->isRobot   = $agent->isRobot() ?? $visitor->isRobot;
        $visitor->languages = $agent->languages() ?? $visitor->languages;
    }
}