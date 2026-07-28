<?php

namespace App\Console\Commands;

use App\Services\ScmCacheService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Throwable;

class BackfillWorldBankCommand extends Command
{
    protected $signature = 'scm:backfill-world-bank {--years=10 : Jumlah tahun historis yang diambil} {--all : Ambil semua negara lokal yang memiliki kode ISO}';

    protected $description = 'Backfill indikator ekonomi historis World Bank secara efisien.';

    private array $indicatorMap = [
        'NY.GDP.MKTP.CD' => 'gdp',
        'FP.CPI.TOTL.ZG' => 'inflation_rate',
        'SP.POP.TOTL' => 'population',
        'NE.EXP.GNFS.CD' => 'exports',
        'NE.IMP.GNFS.CD' => 'imports',
    ];

    public function handle(): int
    {
        $baseUrl = rtrim((string) config('services.world_bank.base_url', 'https://api.worldbank.org/v2'), '/');
        $timeout = max(10, (int) config('services.external_api.timeout', 30));
        $years = max(1, min((int) $this->option('years'), 30));
        $endYear = (int) now()->format('Y');
        $startYear = $endYear - $years + 1;
        $now = now();

        $countries = DB::table('countries')
            ->whereNotNull('country_code')
            ->where('country_code', '!=', '')
            ->select('id', 'country_code', 'name')
            ->orderBy('name')
            ->get();

        if ($countries->isEmpty()) {
            $this->error('Belum ada negara dengan kode ISO untuk backfill World Bank.');
            return self::FAILURE;
        }

        $countriesByCode = [];
        $countriesByName = [];

        foreach ($countries as $country) {
            $code = strtoupper(trim((string) $country->country_code));

            if ($code !== '') {
                $countriesByCode[$code] = $country;
            }

            $countriesByName[$this->normalizeCountryName($country->name)] = $country;
        }

        $estimatedRequests = count($this->indicatorMap);

        $this->info('=== ESTIMASI BACKFILL WORLD BANK ===');
        $this->info('Total negara: ' . $countries->count());
        $this->info("Rentang tahun: {$startYear}-{$endYear}");
        $this->info('Total request awal: ' . $estimatedRequests . ' (1 request global per indikator; bertambah jika pagination World Bank > 1 halaman)');

        $metrics = [
            'total_countries' => $countries->count(),
            'total_requests' => 0,
            'successful_responses' => 0,
            'failed_responses' => 0,
            'records_received' => 0,
            'records_inserted' => 0,
            'records_updated' => 0,
            'records_skipped' => 0,
            'unmatched_rows' => 0,
            'countries_without_data' => 0,
            'indicator_inserted' => array_fill_keys(array_values($this->indicatorMap), 0),
            'indicator_updated' => array_fill_keys(array_values($this->indicatorMap), 0),
        ];

        $snapshots = [];

        foreach ($this->indicatorMap as $indicatorCode => $field) {
            $endpoint = "{$baseUrl}/country/all/indicator/{$indicatorCode}";
            $page = 1;
            $pages = 1;

            do {
                $metrics['total_requests']++;

                try {
                    $response = Http::timeout($timeout)
                        ->retry(2, 1000)
                        ->acceptJson()
                        ->get($endpoint, [
                            'format' => 'json',
                            'date' => $startYear . ':' . $endYear,
                            'per_page' => 20000,
                            'page' => $page,
                        ]);
                } catch (Throwable $exception) {
                    $metrics['failed_responses']++;
                    $this->warn("{$indicatorCode} page {$page} gagal: " . $this->shortError($exception->getMessage()));
                    $this->logWorldBank($baseUrl, 'Partial', "{$indicatorCode} page {$page} gagal: " . $this->shortError($exception->getMessage()), $now);
                    break;
                }

                if (!$response->successful()) {
                    $metrics['failed_responses']++;
                    $this->warn("{$indicatorCode} page {$page} HTTP {$response->status()}");
                    $this->logWorldBank($endpoint, 'Partial', "{$indicatorCode} page {$page} HTTP {$response->status()}", $now);
                    break;
                }

                $payload = $response->json();

                if (!is_array($payload) || !isset($payload[0]) || !is_array($payload[0]) || !isset($payload[1]) || !is_array($payload[1])) {
                    $metrics['failed_responses']++;
                    $this->warn("{$indicatorCode} page {$page} memiliki struktur response tidak valid.");
                    $this->logWorldBank($endpoint, 'Partial', "{$indicatorCode} page {$page} struktur response tidak valid.", $now);
                    break;
                }

                $metrics['successful_responses']++;
                $pages = max(1, (int) data_get($payload, '0.pages', 1));
                $rows = $payload[1];
                $metrics['records_received'] += count($rows);

                foreach ($rows as $row) {
                    $value = data_get($row, 'value');
                    $year = (int) data_get($row, 'date', 0);

                    if ($value === null || !is_numeric($value) || $year < $startYear || $year > $endYear) {
                        $metrics['records_skipped']++;
                        continue;
                    }

                    $country = $this->resolveCountry($row, $countriesByCode, $countriesByName);

                    if (!$country) {
                        $metrics['unmatched_rows']++;
                        $metrics['records_skipped']++;
                        continue;
                    }

                    $snapshots[$country->id][$year]['country'] = $country;
                    $snapshots[$country->id][$year][$field] = $field === 'population'
                        ? (int) $value
                        : (float) $value;
                }

                $page++;
            } while ($page <= $pages);
        }

        foreach ($countries as $country) {
            if (empty($snapshots[$country->id])) {
                $metrics['countries_without_data']++;
                continue;
            }

            foreach ($snapshots[$country->id] as $year => $snapshot) {
                $existing = DB::table('economic_indicators')
                    ->where('country_id', $country->id)
                    ->where('year', $year)
                    ->first();

                $payload = ['updated_at' => $now];

                foreach ($this->indicatorMap as $field) {
                    if (array_key_exists($field, $snapshot)) {
                        $payload[$field] = $snapshot[$field];
                    }
                }

                if ($existing) {
                    DB::table('economic_indicators')->where('id', $existing->id)->update($payload);
                    $metrics['records_updated']++;

                    foreach (array_keys($payload) as $field) {
                        if (isset($metrics['indicator_updated'][$field])) {
                            $metrics['indicator_updated'][$field]++;
                        }
                    }
                } else {
                    DB::table('economic_indicators')->insert(array_merge($payload, [
                        'country_id' => $country->id,
                        'year' => $year,
                        'created_at' => $now,
                    ]));
                    $metrics['records_inserted']++;

                    foreach (array_keys($payload) as $field) {
                        if (isset($metrics['indicator_inserted'][$field])) {
                            $metrics['indicator_inserted'][$field]++;
                        }
                    }
                }
            }
        }

        $dataVersion = ScmCacheService::invalidateGlobalData();
        $message = 'Backfill World Bank selesai. total_negara=' . $metrics['total_countries']
            . ', total_request=' . $metrics['total_requests']
            . ', response_berhasil=' . $metrics['successful_responses']
            . ', response_gagal=' . $metrics['failed_responses']
            . ', record_diterima=' . $metrics['records_received']
            . ', record_baru=' . $metrics['records_inserted']
            . ', record_diperbarui=' . $metrics['records_updated']
            . ', record_dilewati=' . $metrics['records_skipped']
            . ', negara_tanpa_data=' . $metrics['countries_without_data'];

        $this->logWorldBank($baseUrl, $metrics['failed_responses'] > 0 ? 'Partial' : 'Success', $message, $now);

        $this->info('=== HASIL BACKFILL WORLD BANK ===');
        $this->info('Total negara: ' . $metrics['total_countries']);
        $this->info('Total request: ' . $metrics['total_requests']);
        $this->info('Response berhasil: ' . $metrics['successful_responses']);
        $this->info('Response gagal: ' . $metrics['failed_responses']);
        $this->info('Record diterima: ' . $metrics['records_received']);
        $this->info('Record baru: ' . $metrics['records_inserted']);
        $this->info('Record diperbarui: ' . $metrics['records_updated']);
        $this->info('Record dilewati: ' . $metrics['records_skipped']);
        $this->info('Negara tanpa data: ' . $metrics['countries_without_data']);
        $this->info('GDP baru/diperbarui: ' . $metrics['indicator_inserted']['gdp'] . '/' . $metrics['indicator_updated']['gdp']);
        $this->info('Inflasi baru/diperbarui: ' . $metrics['indicator_inserted']['inflation_rate'] . '/' . $metrics['indicator_updated']['inflation_rate']);
        $this->info("Cache global diinvalidasi. scm:data-version={$dataVersion}");

        return $metrics['failed_responses'] > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function resolveCountry(array $row, array $countriesByCode, array $countriesByName): ?object
    {
        $code = strtoupper(trim((string) data_get($row, 'countryiso3code', data_get($row, 'country.id', ''))));
        $name = $this->normalizeCountryName((string) data_get($row, 'country.value', ''));

        return $countriesByCode[$code]
            ?? $countriesByName[$name]
            ?? null;
    }

    private function normalizeCountryName(?string $name): string
    {
        $name = strtolower(trim((string) $name));
        $name = str_replace(['&', ',', '.', "'", '(', ')', '-', '_'], [' and ', ' ', ' ', '', ' ', ' ', ' ', ' '], $name);

        $aliases = [
            'bahamas the' => 'bahamas',
            'brunei darussalam' => 'brunei',
            'china' => 'china',
            'czechia' => 'czech republic',
            'egypt arab rep' => 'egypt',
            'gambia the' => 'gambia',
            'iran islamic rep' => 'iran',
            'korea rep' => 'south korea',
            'korea dem people s rep' => 'north korea',
            'lao pdr' => 'laos',
            'russian federation' => 'russia',
            'slovak republic' => 'slovakia',
            'turkiye' => 'turkey',
            'united states' => 'united states',
            'united states of america' => 'united states',
            'venezuela rb' => 'venezuela',
            'viet nam' => 'vietnam',
            'yemen rep' => 'yemen',
        ];

        $name = trim((string) preg_replace('/\s+/', ' ', $name));

        return $aliases[$name] ?? $name;
    }

    private function logWorldBank(string $endpoint, string $status, string $message, $now): void
    {
        DB::table('api_logs')->insert([
            'api_name' => 'World Bank API',
            'endpoint' => $endpoint,
            'status' => $status,
            'message' => substr($message, 0, 1000),
            'requested_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    private function shortError(string $message): string
    {
        return substr($message, 0, 220);
    }
}
