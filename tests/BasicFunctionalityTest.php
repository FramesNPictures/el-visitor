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

        /** @var \FNP\ElVisitor\Services\VisitorService $s */
        $s = app(\FNP\ElVisitor\Services\VisitorService::class);
        $v = $s->visitor();
//        (new \FNP\ElVisitor\Plugins\Services\LocationByIpinfoIO(env('TOKEN_IPINFO')))->apply($v);
//        (new \FNP\ElVisitor\Plugins\Services\DataByAbuseIPDB(env('TOKEN_ABUSEIP')))->apply($v);
        (new \FNP\ElVisitor\Plugins\Services\DataByLocalDatabase())->apply($v);

        dd($v);
    }
}