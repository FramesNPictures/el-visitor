<?php

namespace FNP\ElVisitor;

use Fnp\ElModule\ElModule;
use Fnp\ElModule\Features\ModuleConfigMerge;
use Fnp\ElModule\Features\ModuleSingletons;
use FNP\ElVisitor\Models\Visitor;
use FNP\ElVisitor\Services\VisitorService;

class ElVisitorModule extends ElModule
{
    use ModuleSingletons;
    use ModuleConfigMerge;

    public function defineSingletons(): array
    {
        return [
            VisitorService::class => VisitorService::class,
            Visitor::class        => function () {
                return app(VisitorService::class)->visitor();
            },
        ];
    }

    public function defineConfigMergeFiles(): array
    {
        $this->publishes([
            __DIR__.'/../config/visitor.php' => config_path('visitor.php'),
        ], 'config');

        return [
            'visitor' => __DIR__.'/../config/visitor.php',
        ];
    }
}