<?php

namespace App\Console\Commands;

use App\Services\GlobalDataSyncService;
use Illuminate\Console\Command;

class SyncCountriesCommand extends Command
{
    protected $signature = 'scm:sync-countries';

    protected $description = 'Sinkronisasi penuh negara dari REST Countries.';

    public function handle(GlobalDataSyncService $service): int
    {
        $result = $service->syncCountries();

        $this->info("Countries source={$result['sourceTotal']} inserted={$result['inserted']} updated={$result['updated']} skipped={$result['skipped']} failed={$result['failed']}.");

        return $result['failed'] > 0 ? self::FAILURE : self::SUCCESS;
    }
}
