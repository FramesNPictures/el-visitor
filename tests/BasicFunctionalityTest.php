<?php

use Illuminate\Contracts\Config\Repository;
use Orchestra\Testbench\Bootstrap\LoadEnvironmentVariables;

class BasicFunctionalityTest extends Orchestra\Testbench\TestCase
{
    protected function getEnvironmentSetUp($app)
    {
        // make sure, our .env file is loaded
        $app->useEnvironmentPath(__DIR__ . '/..');
        $app->bootstrapWith([LoadEnvironmentVariables::class]);
        parent::getEnvironmentSetUp($app);
    }

    protected function defineEnvironment($app)
    {
        // Setup default database to use sqlite :memory:
        tap($app['config'], function (Repository $config) {
            $config->set('visitor', require __DIR__ . '/../config/visitor.php');
        });
    }

    public function test_getting_data()
    {
        $_SERVER['REMOTE_ADDR'] = '132.198.200.196';
        $_SERVER['REMOTE_ADDR'] = '23.228.130.134';
        $_SERVER['REMOTE_ADDR'] = '62.210.243.153';
        $_SERVER['REMOTE_ADDR'] = '2607:fb90:e33c:c367:8c19:2ff:fe47:d1bb';
//        $_SERVER['REMOTE_ADDR'] = '2603:7080:b700:9f4c:fd84:4e0e:3f68:4c7c';
//        $_SERVER['REMOTE_ADDR'] = '2.24.127.161';

        /** @var \FNP\ElVisitor\Services\VisitorService $s */
        $s = app(\FNP\ElVisitor\Services\VisitorService::class);
        $v = $s->visitor();

        dd($v);
    }
}