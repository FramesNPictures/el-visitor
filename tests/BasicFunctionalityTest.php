<?php

use Illuminate\Contracts\Config\Repository;
use Orchestra\Testbench\Bootstrap\LoadEnvironmentVariables;

class BasicFunctionalityTest extends Orchestra\Testbench\TestCase
{
    public static function ipAddressProvider(): array
    {
        return [
            ['132.198.200.196', 4],
            ['23.228.130.134', 4],
            ['62.210.243.153', 4],
            ['2607:fb90:e33c:c367:8c19:2ff:fe47:d1bb', 6],
            ['2603:7080:b700:9f4c:fd84:4e0e:3f68:4c7c', 6],
            ['2.24.127.161', 4],
        ];
    }

    /**
     * @dataProvider ipAddressProvider
     */
    public function test_getting_data($ip, $expectedVersion): void
    {
        $_SERVER['REMOTE_ADDR'] = $ip;

        /** @var FNP\ElVisitor\Services\VisitorService $s */
        $s = app(FNP\ElVisitor\Services\VisitorService::class);
        $v = $s->visitor();

        $this->assertInstanceOf(FNP\ElVisitor\Models\Visitor::class, $v);
        $this->assertEquals($ip, $v->ip);
        $this->assertEquals($expectedVersion, $v->ipVersion);
    }

    protected function getEnvironmentSetUp($app): void
    {
        // Make sure, our .env file is loaded
        $app->useEnvironmentPath(__DIR__ . '/..');
        $app->bootstrapWith([LoadEnvironmentVariables::class]);
        parent::getEnvironmentSetUp($app);
    }

    protected function defineEnvironment($app): void
    {
        // Setup default database to use sqlite :memory:
        tap($app['config'], function (Repository $config): void {
            $config->set('visitor', require __DIR__ . '/../config/visitor.php');
        });
    }
}
