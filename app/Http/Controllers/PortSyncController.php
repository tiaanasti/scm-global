<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Throwable;

class PortSyncController extends Controller
{
    public function sync()
    {
        $itemId = (string) config(
            'services.world_port_index.arcgis_item_id',
            '976ae810a25245228747b80191f625d0'
        );

        $itemsBaseUrl = rtrim(
            (string) config(
                'services.world_port_index.arcgis_items_url',
                'https://www.arcgis.com/sharing/rest/content/items'
            ),
            '/'
        );

        $timeout = (int) config('services.external_api.timeout', 60);
        $now = now();
        $metadataUrl = $itemsBaseUrl . '/' . $itemId;

        try {
            /*
            |--------------------------------------------------------------------------
            | 1. Baca metadata item ArcGIS World Port Index
            |--------------------------------------------------------------------------
            */
            $metadataResponse = Http::timeout($timeout)
                ->acceptJson()
                ->get($metadataUrl, [
                    'f' => 'json',
                ]);

            if (!$metadataResponse->successful()) {
                return $this->fail(
                    $metadataUrl,
                    'Metadata World Port Index gagal diambil. HTTP '
                        . $metadataResponse->status()
                        . '. Response: '
                        . substr($metadataResponse->body(), 0, 500),
                    $now
                );
            }

            $metadata = $metadataResponse->json();

            if (!is_array($metadata) || !empty($metadata['error'])) {
                return $this->fail(
                    $metadataUrl,
                    'Metadata World Port Index tidak valid. Response: '
                        . substr($metadataResponse->body(), 0, 500),
                    $now
                );
            }

            $serviceUrl = trim((string) data_get($metadata, 'url', ''));

            if ($serviceUrl === '') {
                return $this->fail(
                    $metadataUrl,
                    'Item ArcGIS World Port Index tidak memiliki URL Feature Service.',
                    $now
                );
            }

            $serviceUrl = rtrim($serviceUrl, '/');

            /*
            |--------------------------------------------------------------------------
            | 2. Tentukan URL layer
            |--------------------------------------------------------------------------
            */
            if (preg_match('#/(FeatureServer|MapServer)/\d+$#i', $serviceUrl)) {
                $layerUrl = $serviceUrl;
            } else {
                $serviceInfoResponse = Http::timeout($timeout)
                    ->acceptJson()
                    ->get($serviceUrl, [
                        'f' => 'json',
                    ]);

                if (!$serviceInfoResponse->successful()) {
                    return $this->fail(
                        $serviceUrl,
                        'Informasi Feature Service gagal diambil. HTTP '
                            . $serviceInfoResponse->status()
                            . '. Response: '
                            . substr($serviceInfoResponse->body(), 0, 500),
                        $now
                    );
                }

                $serviceInfo = $serviceInfoResponse->json();
                $layers = data_get($serviceInfo, 'layers', []);

                if (!is_array($layers) || empty($layers)) {
                    return $this->fail(
                        $serviceUrl,
                        'Feature Service World Port Index tidak memiliki layer.',
                        $now
                    );
                }

                $layerId = data_get($layers, '0.id');

                if ($layerId === null) {
                    return $this->fail(
                        $serviceUrl,
                        'ID layer World Port Index tidak ditemukan.',
                        $now
                    );
                }

                $layerUrl = $serviceUrl . '/' . $layerId;
            }

            /*
            |--------------------------------------------------------------------------
            | 3. Ambil daftar Object ID seluruh port
            |--------------------------------------------------------------------------
            */
            $idsResponse = Http::timeout($timeout)
                ->acceptJson()
                ->get($layerUrl . '/query', [
                    'where' => '1=1',
                    'returnIdsOnly' => 'true',
                    'f' => 'json',
                ]);

            if (!$idsResponse->successful()) {
                return $this->fail(
                    $layerUrl . '/query',
                    'Daftar ID pelabuhan gagal diambil. HTTP '
                        . $idsResponse->status()
                        . '. Response: '
                        . substr($idsResponse->body(), 0, 500),
                    $now
                );
            }

            $idsPayload = $idsResponse->json();
            $objectIds = data_get($idsPayload, 'objectIds', []);

            if (!is_array($objectIds) || empty($objectIds)) {
                return $this->fail(
                    $layerUrl . '/query',
                    'World Port Index tidak mengembalikan Object ID pelabuhan.',
                    $now
                );
            }

            /*
            |--------------------------------------------------------------------------
            | 4. Siapkan pemetaan negara
            |--------------------------------------------------------------------------
            */
            $countries = DB::table('countries')
                ->select('id', 'country_code', 'name')
                ->get();

            $countriesByCode = [];
            $countriesByName = [];

            foreach ($countries as $country) {
                $code = strtoupper(trim((string) $country->country_code));

                if ($code !== '') {
                    $countriesByCode[$code] = $country;
                }

                $normalizedName = $this->normalizeCountryName($country->name);

                if ($normalizedName !== '') {
                    $countriesByName[$normalizedName] = $country;
                }
            }

            $insertedCount = 0;
            $updatedCount = 0;
            $skippedCount = 0;
            $unmatchedCountryCount = 0;
            $receivedCount = 0;

            /*
            |--------------------------------------------------------------------------
            | 5. Ambil detail port per batch
            |--------------------------------------------------------------------------
            */
            foreach (array_chunk($objectIds, 500) as $objectIdChunk) {
                $featuresResponse = Http::timeout($timeout)
                    ->acceptJson()
                    ->get($layerUrl . '/query', [
                        'objectIds' => implode(',', $objectIdChunk),
                        'outFields' => '*',
                        'returnGeometry' => 'true',
                        'outSR' => 4326,
                        'f' => 'json',
                    ]);

                if (!$featuresResponse->successful()) {
                    DB::table('api_logs')->insert([
                        'api_name' => 'World Port Index',
                        'endpoint' => $layerUrl . '/query',
                        'status' => 'Failed',
                        'message' => substr(
                            'Satu batch pelabuhan gagal. HTTP '
                                . $featuresResponse->status()
                                . '. Response: '
                                . $featuresResponse->body(),
                            0,
                            1000
                        ),
                        'requested_at' => $now,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);

                    $skippedCount += count($objectIdChunk);
                    continue;
                }

                $features = data_get($featuresResponse->json(), 'features', []);

                if (!is_array($features)) {
                    $skippedCount += count($objectIdChunk);
                    continue;
                }

                $receivedCount += count($features);

                DB::transaction(function () use (
                    $features,
                    $countriesByCode,
                    $countriesByName,
                    $now,
                    &$insertedCount,
                    &$updatedCount,
                    &$skippedCount,
                    &$unmatchedCountryCount
                ) {
                    foreach ($features as $feature) {
                        $attributes = data_get($feature, 'attributes', []);
                        $geometry = data_get($feature, 'geometry', []);

                        if (!is_array($attributes)) {
                            $skippedCount++;
                            continue;
                        }

                        $attributes = $this->lowercaseKeys($attributes);

                        $portName = $this->firstValue($attributes, [
                            'main_port_',
                            'main_port_name',
                            'port_name',
                            'portname',
                            'port',
                            'name',
                        ]);

                        if ($portName === null) {
                            $skippedCount++;
                            continue;
                        }

                        $portName = substr(trim($portName), 0, 255);

                        $countryCodeOrName = $this->firstValue($attributes, [
                            'countrycode',
                            'country_code',
                            'country',
                            'country_name',
                            'nation',
                            'iso2',
                            'iso_a2',
                            'iso3',
                            'iso_a3',
                        ]);

                        $countryNameValue = $this->firstValue($attributes, [
                            'country',
                            'country_name',
                            'nation',
                            'countrycode',
                        ]);

                        $country = null;

                        if ($countryCodeOrName !== null) {
                            $candidate = strtoupper(trim($countryCodeOrName));
                            $country = $countriesByCode[$candidate] ?? null;
                        }

                        if (!$country && $countryNameValue !== null) {
                            $normalized = $this->normalizeCountryName(
                                $countryNameValue
                            );

                            $country = $countriesByName[$normalized] ?? null;
                        }

                        if (!$country && $countryCodeOrName !== null) {
                            $normalized = $this->normalizeCountryName(
                                $countryCodeOrName
                            );

                            $country = $countriesByName[$normalized] ?? null;
                        }

                        if (!$country) {
                            $unmatchedCountryCount++;
                            continue;
                        }

                        $city = $this->firstValue($attributes, [
                            'city',
                            'locality',
                            'municipality',
                            'town',
                        ]);

                        $latitude = $this->numericValue(
                            data_get($geometry, 'y')
                                ?? $this->firstValue($attributes, [
                                    'latitude',
                                    'lat',
                                    'y',
                                ])
                        );

                        $longitude = $this->numericValue(
                            data_get($geometry, 'x')
                                ?? $this->firstValue($attributes, [
                                    'longitude',
                                    'lon',
                                    'lng',
                                    'x',
                                ])
                        );

                        if (
                            $latitude === null
                            || $longitude === null
                            || $latitude < -90
                            || $latitude > 90
                            || $longitude < -180
                            || $longitude > 180
                        ) {
                            $skippedCount++;
                            continue;
                        }

                        $exists = DB::table('ports')
                            ->where('country_id', $country->id)
                            ->where('name', $portName)
                            ->exists();

                        DB::table('ports')->updateOrInsert(
                            [
                                'country_id' => $country->id,
                                'name' => $portName,
                            ],
                            [
                                'city' => $city
                                    ? substr(trim($city), 0, 255)
                                    : null,
                                'country_name' => $country->name,
                                'latitude' => round($latitude, 7),
                                'longitude' => round($longitude, 7),

                                /*
                                 * World Port Index berisi profil/fasilitas port,
                                 * bukan status operasional real-time.
                                 */
                                'status' => 'Normal',
                                'port_risk_score' => 20,
                                'created_at' => $exists ? DB::raw('created_at') : $now,
                                'updated_at' => $now,
                            ]
                        );

                        if ($exists) {
                            $updatedCount++;
                        } else {
                            $insertedCount++;
                        }
                    }
                });
            }

            /*
            |--------------------------------------------------------------------------
            | 6. Hapus port dummy setelah data nyata berhasil masuk
            |--------------------------------------------------------------------------
            */
            if (($insertedCount + $updatedCount) > 0) {
                DB::table('ports')
                    ->where('name', 'like', 'Main Port of %')
                    ->delete();
            }

            $message = 'Sinkronisasi World Port Index selesai. '
                . 'Data diterima: '
                . $receivedCount
                . ', port baru: '
                . $insertedCount
                . ', port diperbarui: '
                . $updatedCount
                . ', dilewati: '
                . $skippedCount
                . ', negara tidak cocok: '
                . $unmatchedCountryCount
                . '.';

            DB::table('api_logs')->insert([
                'api_name' => 'World Port Index',
                'endpoint' => $layerUrl,
                'status' => ($insertedCount + $updatedCount) > 0
                    ? 'Success'
                    : 'Failed',
                'message' => substr($message, 0, 1000),
                'requested_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            if (($insertedCount + $updatedCount) === 0) {
                return redirect()
                    ->route('admin.index')
                    ->with(
                        'error',
                        'World Port Index berhasil dihubungi, tetapi belum ada pelabuhan yang tersimpan. Periksa API Logs.'
                    );
            }

            return redirect()
                ->route('admin.index')
                ->with('success', $message);
        } catch (Throwable $exception) {
            return $this->fail(
                $metadataUrl,
                'Sinkronisasi World Port Index gagal: '
                    . $exception->getMessage(),
                $now
            );
        }
    }

    private function lowercaseKeys(array $attributes): array
    {
        $normalized = [];

        foreach ($attributes as $key => $value) {
            $normalized[strtolower((string) $key)] = $value;
        }

        return $normalized;
    }

    private function firstValue(array $attributes, array $keys): ?string
    {
        foreach ($keys as $key) {
            $key = strtolower($key);

            if (!array_key_exists($key, $attributes)) {
                continue;
            }

            $value = $attributes[$key];

            if (is_string($value) || is_numeric($value)) {
                $value = trim((string) $value);

                if ($value !== '') {
                    return $value;
                }
            }
        }

        return null;
    }

    private function numericValue(mixed $value): ?float
    {
        return is_numeric($value) ? (float) $value : null;
    }

    private function normalizeCountryName(?string $name): string
    {
        $name = strtolower(trim((string) $name));

        $aliases = [
            'united states of america' => 'united states',
            'u s a' => 'united states',
            'russian federation' => 'russia',
            'viet nam' => 'vietnam',
            'lao peoples democratic republic' => 'laos',
            'lao pdr' => 'laos',
            'korea republic of' => 'south korea',
            'korea rep' => 'south korea',
            'korea democratic peoples republic of' => 'north korea',
            'iran islamic republic of' => 'iran',
            'syrian arab republic' => 'syria',
            'bolivia plurinational state of' => 'bolivia',
            'venezuela bolivarian republic of' => 'venezuela',
            'tanzania united republic of' => 'tanzania',
            'moldova republic of' => 'moldova',
            'brunei darussalam' => 'brunei',
            'czechia' => 'czech republic',
            'turkiye' => 'turkey',
            'bahamas the' => 'bahamas',
            'gambia the' => 'gambia',
        ];

        $name = str_replace(
            ['&', ',', '.', "'", '(', ')', '-', '_'],
            [' and ', ' ', ' ', '', ' ', ' ', ' ', ' '],
            $name
        );

        $name = preg_replace('/\s+/', ' ', $name);
        $name = trim((string) $name);

        return $aliases[$name] ?? $name;
    }

    private function fail(string $endpoint, string $message, $now)
    {
        DB::table('api_logs')->insert([
            'api_name' => 'World Port Index',
            'endpoint' => $endpoint,
            'status' => 'Failed',
            'message' => substr($message, 0, 1000),
            'requested_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return redirect()
            ->route('admin.index')
            ->with('error', $message);
    }
}