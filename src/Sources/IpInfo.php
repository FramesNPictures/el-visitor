<?php

namespace FNP\ElVisitor\Sources;

use Carbon\Carbon;
use Fnp\ElHelper\Obj;
use FNP\ElVisitor\Interfaces\IpSource;
use FNP\ElVisitor\Models\Visitor;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class IpInfo implements IpSource
{
    public function apply(Visitor $visitor, string $ip): void
    {
        $this->ip = $ip;

        try {
            if (Str::startsWith($ip, ['10.', '192.168.', '172.16'])) {
                // Non routable IP
                return;
            }

            $data = Cache::remember(
                Obj::key(__CLASS__, $ip),
                Carbon::now()->addDays(365),
                function () use ($ip) {
                    $r = Http::get('ipinfo.io/'.$ip.'?token='.env('IPINFO_IO_TOKEN'));
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