<?php

namespace FNP\ElVisitor\Console;

use Illuminate\Console\Command;

class AppVisitorUpdateCommand extends Command
{
    protected $signature = 'app:visitor:update';

    protected $description = 'Update local IP databases';

    public function handle(): int
    {
        $rootFolder = realpath(__DIR__ . '/../..');

        $this->info('Updating local IP geolocation databases...');

        $exitCode = 0;
        passthru('cd ' . escapeshellarg($rootFolder) . ' && ./usr/update-mmdb', $exitCode);

        if ($exitCode !== 0) {
            $this->error('Could not update the local IP geolocation databases.');

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
