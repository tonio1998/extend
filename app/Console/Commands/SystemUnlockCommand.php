<?php

namespace App\Console\Commands;

use App\Services\DSS\SystemLicenseService;
use Illuminate\Console\Command;

class SystemUnlockCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:unlock {key? : Master Reactivation Key issued by Lead Architect Antonio Jr. L. Piloton}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reactivate and restore system license integrity using the Master Reactivation Key';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->newLine();
        $this->line('<fg=yellow;options=bold>================================================================================</>');
        $this->line('<fg=yellow;options=bold>          RIE-EXTEND SYSTEM REACTIVATION & RESTORATION CONSOLE                  </>');
        $this->line('<fg=yellow;options=bold>================================================================================</>');
        $this->newLine();

        $key = $this->argument('key');
        if (! $key) {
            $key = $this->secret('Enter Master Reactivation Key (provided by Antonio Jr. L. Piloton):');
        }

        if (! $key) {
            $this->error('Reactivation aborted: No key provided. Please contact antoniojrpiloton@gmail.com.');

            return self::FAILURE;
        }

        $restored = SystemLicenseService::restoreLicenseWithKey((string) $key);
        if (! $restored) {
            $this->error('INVALID AUTHORIZATION KEY: System remains locked. Please contact Lead Systems Architect Antonio Jr. L. Piloton (antoniojrpiloton@gmail.com).');

            return self::FAILURE;
        }

        $this->info('SUCCESS: Official LICENSE.md and provenance certificate restored successfully!');
        $this->info('Lead Systems Architect: Antonio Jr. L. Piloton');
        $this->info('System Integrity Status: [CRYPTOGRAPHICALLY_VERIFIED]');
        $this->newLine();

        return self::SUCCESS;
    }
}
