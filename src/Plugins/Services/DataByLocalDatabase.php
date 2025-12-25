<?php

namespace FNP\ElVisitor\Plugins\Services;

use Exception;
use FNP\ElVisitor\Interfaces\VisitorPlugin;
use FNP\ElVisitor\Models\Visitor;
use Illuminate\Support\Arr;
use MaxMind\Db\Reader;

class DataByLocalDatabase implements VisitorPlugin
{
    public function apply(Visitor $visitor): void
    {
        try {
            if ($visitor->ipVersion == 4) {
                $this->applyDatabase($visitor, 'asn-ipv4.mmdb', function (Visitor $visitor, array $data): void {
                    $visitor->providerId = Arr::get($data, 'autonomous_system_number', $visitor->providerId);
                    $visitor->providerName = Arr::get($data, 'autonomous_system_organization', $visitor->providerName);
                });

                $this->applyDatabase($visitor, 'geolite2-city-ipv4.mmdb', function (Visitor $visitor, array $data): void {
                    $visitor->city = Arr::get($data, 'city', $visitor->city);
                    $visitor->country = Arr::get($data, 'country_code', $visitor->country);
                    $visitor->region = Arr::get($data, 'state1', $visitor->postcode);
                });

                $this->applyDatabase($visitor, 'dbip-city-ipv4.mmdb', function (Visitor $visitor, array $data): void {
                    $visitor->city = Arr::get($data, 'city', $visitor->city);
                    $visitor->country = Arr::get($data, 'country_code', $visitor->country);
                    $visitor->region = Arr::get($data, 'state1', $visitor->postcode);
                });
            }

            if ($visitor->ipVersion == 6) {
                $this->applyDatabase($visitor, 'asn-ipv6.mmdb', function (Visitor $visitor, array $data): void {
                    $visitor->providerId = Arr::get($data, 'autonomous_system_number', $visitor->providerId);
                    $visitor->providerName = Arr::get($data, 'autonomous_system_organization', $visitor->providerName);
                });

                $this->applyDatabase($visitor, 'geolite2-city-ipv6.mmdb', function (Visitor $visitor, array $data): void {
                    $visitor->city = Arr::get($data, 'city', $visitor->city);
                    $visitor->country = Arr::get($data, 'country_code', $visitor->country);
                    $visitor->region = Arr::get($data, 'state1', $visitor->postcode);
                });

                $this->applyDatabase($visitor, 'dbip-city-ipv6.mmdb', function (Visitor $visitor, array $data): void {
                    $visitor->city = Arr::get($data, 'city', $visitor->city);
                    $visitor->country = Arr::get($data, 'country_code', $visitor->country);
                    $visitor->region = Arr::get($data, 'state1', $visitor->postcode);
                });
            }

            if ($visitor->providerId) {
                $visitor->organisation = 'AS' . $visitor->providerId . ' ' . $visitor->providerName;
            }
        } catch (Exception) {
            // Ignore errors
        }
    }

    protected function applyDatabase(Visitor $visitor, string $database, callable $apply): void
    {
        $databasePath = __DIR__ . '/../../../data/' . $database;

        if ( ! file_exists($databasePath)) {
            return;
        }

        $reader = new Reader($databasePath);
        $data = $reader->get($visitor->ip);

        if (is_null($data)) {
            $reader->close();

            return;
        }

        $apply($visitor, $data);
        $reader->close();
    }
}
