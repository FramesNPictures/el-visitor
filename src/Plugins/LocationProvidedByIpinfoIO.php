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

class LocationProvidedByIpinfoIO implements VisitorPlugin
{
    protected ?string $token = null;

    public function __construct(?string $token)
    {
        $this->token = $token;
    }

    public function apply(Visitor $visitor): void
    {
        if (!$this->token) {
            return;
        }

        $ip = $visitor->ip;

        try {
            if (Str::startsWith($ip, ['10.', '192.168.', '172.16'])) {
                // Non routable IP
                return;
            }

            $data = Cache::remember(
                Obj::key(__CLASS__, $ip),
                Carbon::now()->addDays(365),
                function () use ($ip) {
                    $r = Http::get('ipinfo.io/' . $ip . '?token=' . $this->token);
                    return json_decode($r->body(), true);
                },
            );

            $visitor->ip           = $ip;
            $visitor->city         = $data['city'] ?? $visitor->city;
            $visitor->region       = $data['region'] ?? $visitor->region;
            $visitor->country      = $data['country'] ?? $visitor->country;
            $visitor->location     = $data['loc'] ?? $visitor->location;
            $visitor->organisation = $data['org'] ?? $visitor->organisation;
            $visitor->postcode     = $data['postal'] ?? $visitor->postcode;
            $visitor->timezone     = $data['timezone'] ?? $visitor->timezone;
        } catch (\Exception $e) {
            Log::warning('Problem obtaining IPInfo.io', [$e->getMessage()]);
        }
    }
}