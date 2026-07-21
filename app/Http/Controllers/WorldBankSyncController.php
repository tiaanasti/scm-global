<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Throwable;

class WorldBankSyncController extends Controller
{
    public function sync()
    {
        $baseUrl = rtrim(
            config('services.world_bank.base_url', 'https://api.worldbank.org/v2'),
            '/'
        );

        $timeout = (int) config('services.external_api.timeout', 30);
        $now = now();

        /*
        |--------------------------------------------------------------------------
        | Indikator World Bank
        |--------------------------------------------------------------------------
        */
        $indicatorMap = [
            'NY.GDP.MKTP.CD' => 'gdp',
            'FP.CPI.TOTL.ZG' => 'inflation_rate',
            'SP.POP.TOTL' => 'population',
            'NE.EXP.GNFS.CD' => 'exports',
            'NE.IMP.GNFS.CD' => 'imports',
        ];

        /*
        |--------------------------------------------------------------------------
        | Rentang tahun
        |--------------------------------------------------------------------------
        |
        | mrnev/mrv tidak digunakan karena endpoint tersebut pada beberapa
        | request dapat mengembalikan HTTP 400. Sistem mengambil 10 tahun
        | terakhir, lalu memilih nilai terbaru yang tidak null secara lokal.
        |
        */
        $endYear = (int) now()->format('Y');
        $startYear = $endYear - 10;

        $countries = DB::table('countries')
            ->whereNotNull('country_code')
            ->where('country_code', '!=', '')
            ->select('id', 'country_code', 'name')
            ->orderBy('name')
            ->get();

        if ($countries->isEmpty()) {
            return redirect()
                ->route('admin.index')
                ->with('error', 'Belum ada negara yang dapat disinkronkan.');
        }

        /*
        |--------------------------------------------------------------------------
        | Pemetaan negara
        |--------------------------------------------------------------------------
        */
        $countriesByCode = [];
        $countriesByName = [];

        foreach ($countries as $country) {
            $countryCode = strtoupper(trim((string) $country->country_code));

            if ($countryCode !== '') {
                $countriesByCode[$countryCode] = $country;
            }

            $normalizedName = $this->normalizeCountryName($country->name);

            if ($normalizedName !== '') {
                $countriesByName[$normalizedName] = $country;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Snapshot terbaru per negara
        |--------------------------------------------------------------------------
        */
        $snapshots = [];

        $successfulIndicators = 0;
        $failedIndicators = 0;
        $rowsReceived = 0;
        $unmatchedRows = 0;
        $errorMessages = [];

        try {
            foreach ($indicatorMap as $indicatorCode => $databaseField) {
                $endpoint = $baseUrl
                    . '/country/all/indicator/'
                    . $indicatorCode;

                /*
                 * World Bank resmi mendukung:
                 * format=json
                 * date=YYYY:YYYY
                 * per_page
                 */
                $response = Http::timeout($timeout)
                    ->acceptJson()
                    ->get($endpoint, [
                        'format' => 'json',
                        'date' => $startYear . ':' . $endYear,
                        'per_page' => 5000,
                    ]);

                if (!$response->successful()) {
                    $failedIndicators++;

                    $responseBody = trim($response->body());

                    $errorMessages[] = $indicatorCode
                        . ' HTTP '
                        . $response->status();

                    DB::table('api_logs')->insert([
                        'api_name' => 'World Bank API',
                        'endpoint' => $endpoint,
                        'status' => 'Failed',
                        'message' => substr(
                            'Indikator '
                            . $indicatorCode
                            . ' gagal. HTTP '
                            . $response->status()
                            . '. Response: '
                            . ($responseBody !== '' ? $responseBody : '-'),
                            0,
                            1000
                        ),
                        'requested_at' => $now,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);

                    continue;
                }

                $responseData = $response->json();

                /*
                 * Respons indikator World Bank:
                 * [
                 *     metadata,
                 *     daftar_data
                 * ]
                 */
                if (
                    !is_array($responseData)
                    || !array_key_exists(1, $responseData)
                    || !is_array($responseData[1])
                ) {
                    $failedIndicators++;

                    $errorMessages[] = $indicatorCode
                        . ' memiliki struktur respons tidak valid';

                    DB::table('api_logs')->insert([
                        'api_name' => 'World Bank API',
                        'endpoint' => $endpoint,
                        'status' => 'Failed',
                        'message' => substr(
                            'Struktur respons indikator '
                            . $indicatorCode
                            . ' tidak valid. Response: '
                            . $response->body(),
                            0,
                            1000
                        ),
                        'requested_at' => $now,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);

                    continue;
                }

                $rows = $responseData[1];

                if (empty($rows)) {
                    $failedIndicators++;

                    $errorMessages[] = $indicatorCode
                        . ' tidak mengembalikan data';

                    DB::table('api_logs')->insert([
                        'api_name' => 'World Bank API',
                        'endpoint' => $endpoint,
                        'status' => 'Failed',
                        'message' => 'Indikator '
                            . $indicatorCode
                            . ' tidak mengembalikan data untuk rentang '
                            . $startYear
                            . '-'
                            . $endYear
                            . '.',
                        'requested_at' => $now,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);

                    continue;
                }

                $successfulIndicators++;
                $rowsReceived += count($rows);

                /*
                |--------------------------------------------------------------------------
                | Ambil nilai terbaru tidak null secara lokal
                |--------------------------------------------------------------------------
                */
                foreach ($rows as $row) {
                    $value = data_get($row, 'value');
                    $year = (int) data_get($row, 'date', 0);

                    if (
                        $value === null
                        || !is_numeric($value)
                        || $year <= 0
                    ) {
                        continue;
                    }

                    $worldBankCode = strtoupper(
                        trim((string) data_get($row, 'country.id', ''))
                    );

                    $worldBankName = trim(
                        (string) data_get($row, 'country.value', '')
                    );

                    $country = $countriesByCode[$worldBankCode] ?? null;

                    if (!$country && $worldBankName !== '') {
                        $normalizedWorldBankName = $this->normalizeCountryName(
                            $worldBankName
                        );

                        $country = $countriesByName[
                            $normalizedWorldBankName
                        ] ?? null;
                    }

                    if (!$country) {
                        $unmatchedRows++;
                        continue;
                    }

                    if (!isset($snapshots[$country->id])) {
                        $snapshots[$country->id] = [
                            'country' => $country,
                            'gdp' => null,
                            'inflation_rate' => null,
                            'population' => null,
                            'exports' => null,
                            'imports' => null,
                            'years' => [],
                        ];
                    }

                    $existingYear = (int) (
                        $snapshots[$country->id]['years'][$databaseField]
                        ?? 0
                    );

                    /*
                     * Simpan hanya nilai dengan tahun paling baru.
                     */
                    if ($year >= $existingYear) {
                        $snapshots[$country->id][$databaseField] = (float) $value;
                        $snapshots[$country->id]['years'][$databaseField] = $year;
                    }
                }
            }

            $updatedCountries = 0;
            $skippedCountries = 0;

            foreach ($countries as $country) {
                $snapshot = $snapshots[$country->id] ?? null;

                if (!$snapshot) {
                    $skippedCountries++;
                    continue;
                }

                $years = array_values($snapshot['years']);

                $targetYear = !empty($years)
                    ? max($years)
                    : $endYear;

                DB::transaction(function () use (
                    $country,
                    $snapshot,
                    $targetYear,
                    $now,
                    &$updatedCountries
                ) {
                    $existingLatest = DB::table('economic_indicators')
                        ->where('country_id', $country->id)
                        ->orderByDesc('year')
                        ->first();

                    $existingTarget = DB::table('economic_indicators')
                        ->where('country_id', $country->id)
                        ->where('year', $targetYear)
                        ->first();

                    $payload = [
                        'gdp' => $snapshot['gdp']
                            ?? data_get($existingLatest, 'gdp', 0),

                        'inflation_rate' => $snapshot['inflation_rate']
                            ?? data_get($existingLatest, 'inflation_rate', 0),

                        'population' => $snapshot['population']
                            ?? data_get($existingLatest, 'population', 0),

                        'exports' => $snapshot['exports']
                            ?? data_get($existingLatest, 'exports', 0),

                        'imports' => $snapshot['imports']
                            ?? data_get($existingLatest, 'imports', 0),

                        'updated_at' => $now,
                    ];

                    if ($existingTarget) {
                        DB::table('economic_indicators')
                            ->where('id', $existingTarget->id)
                            ->update($payload);
                    } else {
                        $payload['country_id'] = $country->id;
                        $payload['year'] = $targetYear;
                        $payload['created_at'] = $now;

                        DB::table('economic_indicators')->insert($payload);
                    }

                    $updatedCountries++;
                });
            }

            $status = $updatedCountries > 0
                ? 'Success'
                : 'Failed';

            $message = 'Sinkronisasi World Bank selesai. '
                . 'Negara diperbarui: '
                . $updatedCountries
                . ', negara tanpa data/cocok: '
                . $skippedCountries
                . ', indikator berhasil: '
                . $successfulIndicators
                . ', indikator gagal: '
                . $failedIndicators
                . ', baris diterima: '
                . $rowsReceived
                . ', baris tidak cocok: '
                . $unmatchedRows
                . '.';

            if (!empty($errorMessages)) {
                $message .= ' Detail: '
                    . implode('; ', $errorMessages)
                    . '.';
            }

            DB::table('api_logs')->insert([
                'api_name' => 'World Bank API',
                'endpoint' => $baseUrl,
                'status' => $status,
                'message' => substr($message, 0, 1000),
                'requested_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            if ($updatedCountries === 0) {
                return redirect()
                    ->route('admin.index')
                    ->with(
                        'error',
                        'World Bank API terhubung, tetapi belum ada negara yang berhasil diperbarui. Periksa API Logs terbaru.'
                    );
            }

            return redirect()
                ->route('admin.index')
                ->with('success', $message);
        } catch (Throwable $exception) {
            DB::table('api_logs')->insert([
                'api_name' => 'World Bank API',
                'endpoint' => $baseUrl,
                'status' => 'Failed',
                'message' => substr(
                    'Sinkronisasi gagal: '
                    . $exception->getMessage(),
                    0,
                    1000
                ),
                'requested_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            return redirect()
                ->route('admin.index')
                ->with(
                    'error',
                    'Sinkronisasi World Bank gagal: '
                    . $exception->getMessage()
                );
        }
    }

    private function normalizeCountryName(?string $name): string
    {
        $name = strtolower(trim((string) $name));

        $replacements = [
            '&' => ' and ',
            ',' => ' ',
            '.' => ' ',
            "'" => '',
            'the bahamas' => 'bahamas',
            'bahamas, the' => 'bahamas',
            'the gambia' => 'gambia',
            'gambia, the' => 'gambia',
            'egypt, arab rep.' => 'egypt',
            'iran, islamic rep.' => 'iran',
            'korea, rep.' => 'south korea',
            'korea, dem. peoples rep.' => 'north korea',
            'russian federation' => 'russia',
            'slovak republic' => 'slovakia',
            'syrian arab republic' => 'syria',
            'turkiye' => 'turkey',
            'venezuela, rb' => 'venezuela',
            'yemen, rep.' => 'yemen',
            'congo, dem. rep.' => 'dr congo',
            'congo, rep.' => 'republic of the congo',
            'czechia' => 'czech republic',
            'lao pdr' => 'laos',
            'viet nam' => 'vietnam',
        ];

        $name = strtr($name, $replacements);
        $name = preg_replace('/\s+/', ' ', $name);

        return trim((string) $name);
    }
}