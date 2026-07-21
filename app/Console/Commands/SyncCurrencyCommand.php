<?php

namespace App\Console\Commands;

use App\Services\SupplyChainSyncService;
use Illuminate\Console\Command;
use Throwable;

class SyncCurrencyCommand extends Command
{
    protected $signature = 'scm:sync-currency';

    protected $description = 'Sinkronisasi data kurs ExchangeRate API untuk negara SCM.';

    public function handle(SupplyChainSyncService $service): int
    {
        try {
            $result = $service->syncCurrency();
            $this->info("Kurs selesai. Negara: {$result['countries']}, berhasil: {$result['success']}, gagal: {$result['failed']}.");

            return $result['failed'] > 0 ? self::FAILURE : self::SUCCESS;
        } catch (Throwable $exception) {
            $this->error('Sinkronisasi kurs gagal: ' . $exception->getMessage());

            return self::FAILURE;
        }
    }
}
