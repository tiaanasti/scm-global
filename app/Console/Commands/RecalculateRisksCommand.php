<?php

namespace App\Console\Commands;

use App\Services\SupplyChainSyncService;
use Illuminate\Console\Command;
use Throwable;

class RecalculateRisksCommand extends Command
{
    protected $signature = 'scm:recalculate-risks';

    protected $description = 'Hitung ulang skor risiko rantai pasok negara SCM.';

    public function handle(SupplyChainSyncService $service): int
    {
        try {
            $result = $service->recalculateRisks();
            $this->info("Risk scoring selesai. Negara: {$result['countries']}, berhasil: {$result['success']}, gagal: {$result['failed']}.");

            return $result['failed'] > 0 ? self::FAILURE : self::SUCCESS;
        } catch (Throwable $exception) {
            $this->error('Risk scoring gagal: ' . $exception->getMessage());

            return self::FAILURE;
        }
    }
}
