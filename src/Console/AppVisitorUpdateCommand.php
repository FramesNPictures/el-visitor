<?php

namespace FNP\ElVisitor\Console;

use Illuminate\Console\Command;

class AppVisitorUpdateCommand extends Command
{
    protected $signature = 'app:visitor:update';
    protected $description = 'Update local IP databases';

    public function handle()
    {
        $dataFolder = __DIR__ . '/../../data';
        $rootFolder = realpath(__DIR__ . '/../..');

        $this->info('Updating local IP geolocation databases...');
        shell_exec('cd "' . $rootFolder . '" && .scripts/update.sh');
    }
}