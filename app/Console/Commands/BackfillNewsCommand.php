<?php

namespace App\Console\Commands;

use App\Services\GlobalDataSyncService;
use Illuminate\Console\Command;

class BackfillNewsCommand extends Command
{
    protected $signature = 'scm:backfill-news {--pages=3 : Jumlah halaman GNews untuk backfill global}';

    protected $description = 'Backfill manual berita global supply chain dari GNews.';

    public function handle(GlobalDataSyncService $service): int
    {
        $pages = max(1, (int) $this->option('pages'));
        $result = $service->backfillGlobalNews($pages);

        $this->info("News fetched={$result['fetched']} inserted={$result['inserted']} duplicates={$result['duplicates']} failed={$result['failed']}.");

        return $result['failed'] > 0 ? self::FAILURE : self::SUCCESS;
    }
}
