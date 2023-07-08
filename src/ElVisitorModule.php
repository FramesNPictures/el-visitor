<?php

namespace FNP\ElVisitor;

use Fnp\ElModule\ElModule;
use Fnp\ElModule\Features\ModuleConfigMerge;
use Fnp\ElModule\Features\ModuleSetupWebApplication;
use Fnp\ElModule\Features\ModuleSingletons;
use FNP\ElVisitor\Middleware\VisitorMiddleware;
use FNP\ElVisitor\Models\Visitor;
use FNP\ElVisitor\Services\VisitorService;
use Illuminate\Foundation\Application;

class ElVisitorModule extends ElModule
{
    use ModuleSingletons;
    use ModuleSetupWebApplication;
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

    public function setupWebApplication(Application $application)
    {
        $application['router']->pushMiddlewareToGroup('web', VisitorMiddleware::class);
    }

    public function defineConfigMergeFiles(): array
    {
        return [
            'visitor' => __DIR__.'/../config/visitor.php',
        ];
    }
}