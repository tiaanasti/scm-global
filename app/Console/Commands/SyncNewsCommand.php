<?php

namespace App\Console\Commands;

use App\Services\GlobalDataSyncService;
use App\Services\SupplyChainSyncService;
use Illuminate\Console\Command;
use Throwable;

class SyncNewsCommand extends Command
{
    protected $signature = 'scm:sync-news';

    protected $description = 'Sinkronisasi berita GNews untuk negara SCM.';

    public function handle(SupplyChainSyncService $supplyChainSyncService, GlobalDataSyncService $globalDataSyncService): int
    {
        try {
            $countries = $supplyChainSyncService->monitoredCountries();
            $result = $globalDataSyncService->syncWatchlistNews($countries);
            $this->info("Berita selesai. Negara: {$countries->count()}, baru: {$result['inserted']}, duplikat: {$result['duplicates']}, gagal: {$result['failed']}.");

            return $result['failed'] > 0 ? self::FAILURE : self::SUCCESS;
        } catch (Throwable $exception) {
            $this->error('Sinkronisasi berita gagal: ' . $exception->getMessage());

            return self::FAILURE;
        }
    }
}
