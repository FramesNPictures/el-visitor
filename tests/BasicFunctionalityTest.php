<?php

use Illuminate\Contracts\Config\Repository;

class BasicFunctionalityTest extends Orchestra\Testbench\TestCase
{
    protected function defineEnvironment($app)
    {
        // Setup default database to use sqlite :memory:
        tap($app['config'], function (Repository $config) {
            $config->set('visitor', require __DIR__ . '/../config/visitor.php');
        });
    }

    public function test_getting_data()
    {
        $_SERVER['REMOTE_ADDR'] = '2600:1014:b301:2f8:7dc3:f579:9b73:b14e';

        /** @var \FNP\ElVisitor\Services\VisitorService $s */
        $s = app(\FNP\ElVisitor\Services\VisitorService::class);
        $v = $s->visitor();

        dd($v);
    }
}