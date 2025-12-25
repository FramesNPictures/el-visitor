<?php

namespace FNP\ElVisitor\Tests;

use Illuminate\Contracts\Config\Repository;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function defineEnvironment($app)
    {
        tap($app['config'], function (Repository $config): void {
            $config->set('visitor', require __DIR__.'/../config/visitor.php');
        });
    }
}
