<?php

namespace FNP\ElVisitor\Interfaces;

use FNP\ElVisitor\Models\Visitor;

interface IpSource
{
    public function apply(Visitor $visitor, string $ip): void;
}