<?php

namespace FNP\ElVisitor\Plugins\Services;

use Carbon\Carbon;
use Fnp\ElHelper\Arr;
use Fnp\ElHelper\Obj;
use FNP\ElVisitor\Interfaces\VisitorPlugin;
use FNP\ElVisitor\Models\Visitor;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LocationByDbIpCom implements VisitorPlugin
{
    protected ?string $token = null;

    public function __construct(?string $token = 'free')
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
                    $r = Http::get('https://api.db-ip.com/v2/' . $this->token . '/' . $ip);
                    return json_decode($r->body(), true);
                },
            );

            $visitor->city = Arr::get($data, 'city', $visitor->city);
            $visitor->region = Arr::get($data, 'stateProv', $visitor->region);
            $visitor->country = Arr::get($data,'countryCode', $visitor->country);
        } catch (\Exception $e) {
            Log::warning('Could not obtain data from db-ip.com', [$e->getMessage()]);
        }
    }
}