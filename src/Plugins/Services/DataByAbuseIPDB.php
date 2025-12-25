<?php

namespace FNP\ElVisitor\Plugins\Services;

use Carbon\Carbon;
use Exception;
use Fnp\ElHelper\Arr;
use Fnp\ElHelper\Obj;
use FNP\ElVisitor\Interfaces\VisitorPlugin;
use FNP\ElVisitor\Models\Visitor;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DataByAbuseIPDB implements VisitorPlugin
{
    public function __construct(protected ?string $token) {}

    public function apply(Visitor $visitor): void
    {
        if ( ! $this->token) {
            return;
        }

        $ip = $visitor->ip;

        try {
            if (Str::startsWith($ip, ['10.', '192.168.', '172.16'])) {
                // Non routable IP
                return;
            }

            $data = Cache::remember(
                Obj::key(self::class, $ip),
                Carbon::now()->addDays(365),
                function () use ($ip) {
                    $r = Http::withHeaders([
                        'Accept' => 'application/json',
                        'Key' => $this->token,
                    ])->get('https://api.abuseipdb.com/api/v2/check', ['ipAddress' => $ip]);

                    return json_decode($r->body(), true);
                },
            );

            $visitor->country = Arr::get($data, 'data.countryCode', $visitor->country);
            $visitor->organisation = Arr::get($data, 'data.isp', $visitor->organisation);
            $visitor->abuseScore = Arr::get($data, 'data.abuseConfidenceScore', $visitor->abuseScore);
            $visitor->abuseReports = Arr::get($data, 'data.totalReports', $visitor->abuseReports);
            $visitor->isTor = Arr::get($data, 'data.isTor', $visitor->isTor);
            $visitor->extra = array_merge($visitor->extra, [
                'domain' => Arr::get($data, 'data.domain'),
                'hostnames' => Arr::get($data, 'data.hostnames', []),
            ]);
        } catch (Exception $e) {
            Log::warning('Problem obtaining AbuseIPDB', [$e->getMessage()]);
        }
    }
}
