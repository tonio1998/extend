<?php

namespace App\Console\Commands;

use App\Services\DSS\SystemLicenseService;
use Illuminate\Console\Command;

class SystemLicenseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:license {--verify : Verify system authorship and provenance integrity}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display official software license, authorship certificate, and provenance integrity';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $meta = SystemLicenseService::getProvenance();

        $this->newLine();
        $this->line('<fg=green;options=bold>================================================================================</>');
        $this->line('<fg=cyan;options=bold>  ██████╗ ██╗███████╗      ███████╗██╗  ██╗████████╗███████╗███╗   ██╗██████╗   </>');
        $this->line('<fg=cyan;options=bold>  ██╔══██╗██║██╔════╝      ██╔════╝╚██╗██╔╝╚══██╔══╝██╔════╝████╗  ██║██╔══██╗  </>');
        $this->line('<fg=cyan;options=bold>  ██████╔╝██║█████╗  █████╗█████╗   ╚███╔╝    ██║   █████╗  ██╔██╗ ██║██║  ██║  </>');
        $this->line('<fg=cyan;options=bold>  ██╔══██╗██║██╔══╝  ╚════╝██╔══╝   ██╔██╗    ██║   ██╔══╝  ██║╚██╗██║██║  ██║  </>');
        $this->line('<fg=cyan;options=bold>  ██║  ██║██║███████╗      ███████╗██╔╝ ██╗   ██║   ███████╗██║ ╚████║██████╔╝  </>');
        $this->line('<fg=cyan;options=bold>  ╚═╝  ╚═╝╚═╝╚══════╝      ╚══════╝╚═╝  ╚═╝   ╚═╝   ╚══════╝╚═╝  ╚═══╝╚═════╝   </>');
        $this->line('<fg=green;options=bold>================================================================================</>');
        $this->line('<fg=yellow;options=bold>               OFFICIAL SYSTEM PROVENANCE & AUTHORSHIP CERTIFICATE              </>');
        $this->line('<fg=green;options=bold>================================================================================</>');
        $this->newLine();

        $this->table(
            ['Attribute', 'Verified Metadata Value'],
            [
                ['Platform', $meta['platform']],
                ['Engine Version', $meta['version']],
                ['Lead Architect & Developer', '<fg=green;options=bold>'.$meta['author'].'</>'],
                ['Professional Role', $meta['role']],
                ['Initial Release Year', $meta['release_year']],
                ['System Fingerprint', '<fg=yellow;options=bold>'.$meta['fingerprint'].'</>'],
                ['Integrity Verification', '<fg=green;options=bold>['.$meta['integrity_status'].']</>'],
                ['Governing Law / Moral Rights', $meta['moral_rights']],
            ]
        );

        $this->newLine();
        $this->info('License Terms: '.$meta['terms']);
        $this->newLine();

        return self::SUCCESS;
    }
}
