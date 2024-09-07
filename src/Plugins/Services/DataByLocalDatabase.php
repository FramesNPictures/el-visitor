<?php

namespace FNP\ElVisitor\Plugins\Services;

use Fnp\ElHelper\Arr;
use FNP\ElVisitor\Interfaces\VisitorPlugin;
use FNP\ElVisitor\Models\Visitor;
use MaxMind\Db\Reader;

class DataByLocalDatabase implements VisitorPlugin
{
    public function apply(Visitor $visitor): void
    {
        $reader = new Reader(__DIR__ . '/../../../data/country_asn.mmdb');
        $data = $reader->get($visitor->ip);

        $visitor->country = Arr::get($data, 'country');
        $visitor->organisation = Arr::get($data, 'asn') . ' ' . Arr::get($data, 'as_name');

        $reader->close();
    }
}