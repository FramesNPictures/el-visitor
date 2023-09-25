<?php

namespace FNP\ElVisitor\Plugins;

use Carbon\Carbon;
use Fnp\ElHelper\Obj;
use FNP\ElVisitor\Interfaces\VisitorPlugin;
use FNP\ElVisitor\Models\Visitor;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CheckIpInfo implements VisitorPlugin
{
    public function apply(Visitor $visitor): void
    {
        $token = config('visitor.services.ip_info_token');

        if (!$token) {
            return;
        }

        try {
            if (Str::startsWith($ip, ['10.', '192.168.', '172.16'])) {
                // Non routable IP
                return;
            }

            $data = Cache::remember(
                Obj::key(__CLASS__, $ip),
                Carbon::now()->addDays(365),
                function () use ($ip, $token) {
                    $r = Http::get('ipinfo.io/' . $ip . '?token=' . $token);
                    return json_decode($r->body(), true);
                },
            );

            $visitor->ip           = $ip;
            $visitor->city         = $data['city'] ?? null;
            $visitor->region       = $data['region'] ?? null;
            $visitor->country      = $data['country'] ?? null;
            $visitor->location     = $data['loc'] ?? null;
            $visitor->organisation = $data['org'] ?? null;
            $visitor->postcode     = $data['postal'] ?? null;
            $visitor->timezone     = $data['timezone'] ?? null;
        } catch (\Exception $e) {
            Log::warning('Problem obtaining IPInfo.io', [$e->getMessage()]);
        }
    }
}