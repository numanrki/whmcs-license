<?php

namespace Numanrki\WhmcsLicense\Console\Commands;

use Illuminate\Console\Command;
use Numanrki\WhmcsLicense\WhmcsLicenseManager;

class VerifyLicenseCommand extends Command
{
    protected $signature = 'whmcs:verify {license} {--domain=} {--ip=} {--dir=}';

    protected $description = 'Verify a WHMCS license';

    public function handle()
    {
        $license = $this->argument('license');
        $context = [
            'domain' => $this->option('domain') ?? null,
            'ip' => $this->option('ip') ?? null,
            'directory' => $this->option('dir') ?? null,
        ];

        $manager = app(WhmcsLicenseManager::class);
        $result = $manager->verify($license, $context);

        if ($result['valid']) {
            $this->info('License is valid.');
            $this->line('Status: ' . $result['data']['status']);
            $this->line('Expires at: ' . ($result['data']['expires_at'] ?? 'N/A'));
            $this->line('Product: ' . ($result['data']['product'] ?? 'N/A'));
            $this->line('Checks: ' . json_encode($result['data']['checks']));
        } else {
            $this->error('License is invalid.');
            $this->line('Error: ' . ($result['error'] ?? 'Unknown error'));
        }
    }
}