<?php

namespace FNP\ElVisitor\Interfaces;

use FNP\ElVisitor\Models\Visitor;

interface VisitorPlugin
{
    public function apply(Visitor $visitor): void;
}