<?php

namespace FNP\ElVisitor\Tests;

use Orchestra\Testbench\Bootstrap\LoadEnvironmentVariables;

abstract class IntegrationTestCase extends TestCase
{
    protected function getEnvironmentSetUp($app): void
    {
        // Make sure, our .env file is loaded
        $app->useEnvironmentPath(__DIR__ . '/..');
        $app->bootstrapWith([LoadEnvironmentVariables::class]);
        parent::getEnvironmentSetUp($app);
    }
}
