<?php

namespace App\Console\Commands;

use App\Services\GlobalDataSyncService;
use App\Services\ScmCacheService;
use Illuminate\Console\Command;

class SyncPortsCommand extends Command
{
    protected $signature = 'scm:sync-ports {--all : Import seluruh objectIds World Port Index}';

    protected $description = 'Sinkronisasi penuh World Port Index.';

    public function handle(GlobalDataSyncService $service): int
    {
        $result = $service->syncPorts((bool) $this->option('all'));

        $this->info("Sinkronisasi World Port Index selesai.");
        $this->info("Sumber: {$result['source_count']}");
        $this->info("Diminta: {$result['requested_count']}");
        $this->info("API diterima: {$result['received_count']}");
        $this->info("Baru: {$result['inserted_count']}");
        $this->info("Diperbarui: {$result['updated_count']}");
        $this->info("Dilewati: {$result['skipped_count']}");
        $this->info("Record gagal: {$result['failed_record_count']}");
        $this->info("Request gagal: {$result['failed_request_count']}");

        if ($result['failed_record_count'] + $result['failed_request_count'] === 0) {
            $dataVersion = ScmCacheService::invalidateGlobalData();
            $this->info("Cache global diinvalidasi. scm:data-version={$dataVersion}");
        }

        return $result['failed_record_count'] + $result['failed_request_count'] > 0 ? self::FAILURE : self::SUCCESS;
    }
}
