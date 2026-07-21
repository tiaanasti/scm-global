<?php

namespace App\Console\Commands;

use App\Services\SupplyChainSyncService;
use Illuminate\Console\Command;
use Throwable;

class SyncWeatherCommand extends Command
{
    protected $signature = 'scm:sync-weather';

    protected $description = 'Sinkronisasi data cuaca Open-Meteo untuk negara SCM.';

    public function handle(SupplyChainSyncService $service): int
    {
        try {
            $result = $service->syncWeather();
            $this->info("Cuaca selesai. Negara: {$result['countries']}, berhasil: {$result['success']}, gagal: {$result['failed']}.");

            return $result['failed'] > 0 ? self::FAILURE : self::SUCCESS;
        } catch (Throwable $exception) {
            $this->error('Sinkronisasi cuaca gagal: ' . $exception->getMessage());

            return self::FAILURE;
        }
    }
}
