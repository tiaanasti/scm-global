<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

class GlobalSyncOrchestratorService
{
    protected $supplyChainSyncService;
    protected $globalDataSyncService;

    public function __construct(
        SupplyChainSyncService $supplyChainSyncService,
        GlobalDataSyncService $globalDataSyncService
    ) {
        $this->supplyChainSyncService = $supplyChainSyncService;
        $this->globalDataSyncService = $globalDataSyncService;
    }

    public function runSync(): array
    {
        $startTime = microtime(true);
        $now = now();

        // 1. Acquire global lock for 180 seconds
        $lock = Cache::lock('scm-global-sync', 180);

        if (!$lock->get()) {
            return [
                'status' => 'success',
                'message' => 'Sinkronisasi sebelumnya masih berjalan.',
                'skipped' => true,
            ];
        }

        $summary = [
            'weather' => ['synced' => 0, 'failed' => 0],
            'currency' => ['synced' => 0, 'failed' => 0, 'skipped' => false],
            'news' => ['synced' => 0, 'failed' => 0, 'skipped' => false],
            'ports' => ['skipped' => true],
            'world_bank' => ['skipped' => true],
            'recalculated_countries_count' => 0,
        ];

        $updatedCountryIds = [];

        try {
            // 2. Weather Sync (Batch of 25 countries, prioritizing watchlist)
            $watchlistIds = DB::table('watchlists')->pluck('country_id')->unique()->toArray();
            
            $priorityCountries = collect();
            if (!empty($watchlistIds)) {
                $priorityCountries = DB::table('countries')
                    ->whereIn('id', $watchlistIds)
                    ->where(function ($query) use ($now) {
                        $query->whereNotExists(function ($q) {
                            $q->select(DB::raw(1))
                              ->from('weather_reports')
                              ->whereColumn('weather_reports.country_id', 'countries.id')
                              ->where('reported_at', '>=', now()->subMinutes(5));
                        });
                    })
                    ->limit(25)
                    ->get();
            }

            $priorityIds = $priorityCountries->pluck('id')->toArray();
            $limit = 25 - count($priorityIds);

            $normalCountries = collect();
            if ($limit > 0) {
                $lastId = Cache::get('scm:weather-last-country-id', 0);
                $normalCountries = DB::table('countries')
                    ->whereNotIn('id', $priorityIds)
                    ->where('id', '>', $lastId)
                    ->orderBy('id', 'asc')
                    ->limit($limit)
                    ->get();

                if ($normalCountries->count() < $limit) {
                    $remaining = $limit - $normalCountries->count();
                    $wrapCountries = DB::table('countries')
                        ->whereNotIn('id', $priorityIds)
                        ->orderBy('id', 'asc')
                        ->limit($remaining)
                        ->get();
                    $normalCountries = $normalCountries->merge($wrapCountries);
                }

                if ($normalCountries->isNotEmpty()) {
                    Cache::put('scm:weather-last-country-id', $normalCountries->last()->id, 1440);
                }
            }

            $weatherCountries = collect($priorityCountries)->merge($normalCountries)->unique('id');

            if ($weatherCountries->isNotEmpty()) {
                $weatherResult = $this->supplyChainSyncService->syncWeather($weatherCountries);
                $summary['weather']['synced'] = $weatherResult['success'];
                $summary['weather']['failed'] = $weatherResult['failed'];

                if ($weatherResult['success'] > 0) {
                    ScmCacheService::invalidateGlobalData();
                }
                
                $updatedCountryIds = array_merge($updatedCountryIds, $weatherCountries->pluck('id')->toArray());
            }

            // 3. Currency Sync (TTL: 10 minutes)
            $lastCurrencySync = Cache::get('scm:currency-last-sync-time');
            if (!$lastCurrencySync || now()->diffInMinutes($lastCurrencySync) >= 10) {
                $currencyResult = $this->supplyChainSyncService->syncCurrency();
                $summary['currency']['synced'] = $currencyResult['success'];
                $summary['currency']['failed'] = $currencyResult['failed'];
                Cache::put('scm:currency-last-sync-time', now(), 1440);

                if ($currencyResult['success'] > 0) {
                    ScmCacheService::invalidateGlobalData();
                }

                // Currency affects all monitored countries
                $monitoredIds = $this->supplyChainSyncService->monitoredCountries()->pluck('id')->toArray();
                $updatedCountryIds = array_merge($updatedCountryIds, $monitoredIds);
            } else {
                $summary['currency']['skipped'] = true;
            }

            // 4. News Sync (TTL: 30 minutes, rotating global & watchlist)
            $lastNewsSync = Cache::get('scm:news-last-sync-time');
            if (!$lastNewsSync || now()->diffInMinutes($lastNewsSync) >= 30) {
                $syncType = Cache::get('scm:news-sync-type', 'global');
                
                if ($syncType === 'global') {
                    $newsResult = $this->globalDataSyncService->backfillGlobalNews(1);
                    $summary['news']['synced'] = $newsResult['inserted'];
                    $summary['news']['failed'] = $newsResult['failed'];
                    Cache::put('scm:news-sync-type', 'watchlist', 1440);

                    if (($newsResult['inserted'] + ($newsResult['updated'] ?? 0)) > 0) {
                        ScmCacheService::invalidateGlobalData();
                    }
                } else {
                    $monitoredCountries = $this->supplyChainSyncService->monitoredCountries();
                    if ($monitoredCountries->isNotEmpty()) {
                        $newsResult = $this->globalDataSyncService->syncWatchlistNews($monitoredCountries, 1);
                        $summary['news']['synced'] = $newsResult['inserted'];
                        $summary['news']['failed'] = $newsResult['failed'];

                        if (($newsResult['inserted'] + ($newsResult['updated'] ?? 0)) > 0) {
                            ScmCacheService::invalidateGlobalData();
                        }
                        
                        $updatedCountryIds = array_merge($updatedCountryIds, $monitoredCountries->pluck('id')->toArray());
                    }
                    Cache::put('scm:news-sync-type', 'global', 1440);
                }
                Cache::put('scm:news-last-sync-time', now(), 1440);
            } else {
                $summary['news']['skipped'] = true;
            }

            // 5. Risk Scoring (Recalculate only updated countries in this cycle)
            $updatedCountryIds = array_values(array_unique(array_filter($updatedCountryIds)));
            if (!empty($updatedCountryIds)) {
                $countriesToRecalculate = DB::table('countries')->whereIn('id', $updatedCountryIds)->get();
                $riskResult = $this->supplyChainSyncService->recalculateRisks($countriesToRecalculate);
                $summary['recalculated_countries_count'] = $riskResult['success'];

                if ($riskResult['success'] > 0) {
                    ScmCacheService::invalidateGlobalData();
                }
            }

            // 6. Global Cache Invalidation & Version Increment
            $dataVersion = ScmCacheService::invalidateGlobalData();

            $duration = round(microtime(true) - $startTime, 2);

            // Log execution result to api_logs
            DB::table('api_logs')->insert([
                'api_name' => 'SCM Global Sync',
                'endpoint' => 'scm:sync-global',
                'status' => 'Success',
                'message' => "Siklus sinkronisasi terpusat berhasil. Cuaca: {$summary['weather']['synced']} synced, {$summary['weather']['failed']} failed. Kurs: " . ($summary['currency']['skipped'] ? 'skipped' : $summary['currency']['synced'] . ' synced') . ". Berita: " . ($summary['news']['skipped'] ? 'skipped' : $summary['news']['synced'] . ' synced') . ". Risiko dihitung ulang: {$summary['recalculated_countries_count']} negara. Data version: {$dataVersion}. Durasi: {$duration}s.",
                'requested_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            return [
                'status' => 'success',
                'message' => 'Sinkronisasi berhasil diselesaikan.',
                'summary' => $summary,
                'duration' => $duration,
            ];
        } catch (Throwable $e) {
            $duration = round(microtime(true) - $startTime, 2);
            Log::error('SCM Global Sync Error: ' . $e->getMessage(), ['exception' => $e]);

            DB::table('api_logs')->insert([
                'api_name' => 'SCM Global Sync',
                'endpoint' => 'scm:sync-global',
                'status' => 'Failed',
                'message' => "Siklus sinkronisasi gagal: " . substr($e->getMessage(), 0, 800),
                'requested_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            return [
                'status' => 'failed',
                'message' => 'Sinkronisasi gagal: ' . $e->getMessage(),
                'duration' => $duration,
            ];
        } finally {
            $lock->release();
        }
    }
}
