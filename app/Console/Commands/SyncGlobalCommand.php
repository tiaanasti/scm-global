<?php

namespace App\Console\Commands;

use App\Services\GlobalSyncOrchestratorService;
use Illuminate\Console\Command;

class SyncGlobalCommand extends Command
{
    protected $signature = 'scm:sync-global';

    protected $description = 'Orkestrasi sinkronisasi data global terpusat SCM.';

    public function handle(GlobalSyncOrchestratorService $orchestrator): int
    {
        $this->info('Memulai sinkronisasi global terpusat SCM...');
        
        $result = $orchestrator->runSync();

        if (isset($result['skipped']) && $result['skipped']) {
            $this->warn($result['message']);
            return self::SUCCESS;
        }

        if ($result['status'] === 'success') {
            $this->info($result['message']);
            if (isset($result['duration'])) {
                $this->info("Durasi eksekusi: {$result['duration']} detik.");
            }
            return self::SUCCESS;
        }

        $this->error($result['message']);
        return self::FAILURE;
    }
}
