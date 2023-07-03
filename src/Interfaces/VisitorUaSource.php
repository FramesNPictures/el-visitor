<?php

namespace FNP\ElVisitor\Interfaces;

use FNP\ElVisitor\Models\Visitor;

interface VisitorUaSource
{
    public function apply(Visitor $visitor, string $ua): void;
}