<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Throwable;

class GlobalDataSyncService
{
    public function syncCountries(): array
    {
        $endpoint = (string) config('services.rest_countries.base_url', 'https://api.restcountries.com/countries/v5');
        $apiKey = config('services.rest_countries.api_key');
        $limit = max(1, min((int) config('services.rest_countries.limit', 100), 100));
        $timeout = (int) config('services.external_api.timeout', 30);
        $offset = 0;
        $inserted = 0;
        $updated = 0;
        $skipped = 0;
        $failed = 0;
        $sourceTotal = 0;
        $now = now();

        if (!$apiKey) {
            $this->logApi('REST Countries API', $endpoint, 'Failed', 'REST_COUNTRIES_API_KEY belum diisi.', $now);

            return compact('endpoint', 'sourceTotal', 'inserted', 'updated', 'skipped', 'failed');
        }

        do {
            try {
                $response = Http::timeout($timeout)->acceptJson()->withToken($apiKey)->get($endpoint, [
                    'limit' => $limit,
                    'offset' => $offset,
                    'response_fields' => 'names.common,codes.alpha_2,capitals,region,currencies,languages,coordinates.lat,coordinates.lng,population',
                ]);
            } catch (Throwable $exception) {
                $failed++;
                $this->logApi('REST Countries API', $endpoint, 'Failed', $this->externalErrorMessage($exception), $now);
                break;
            }

            if (!$response->successful()) {
                $failed++;
                $this->logApi('REST Countries API', $endpoint, 'Failed', $this->httpMessage($response->status()), $now);
                break;
            }

            $items = data_get($response->json(), 'data.objects', []);
            $meta = data_get($response->json(), 'data.meta', []);
            $sourceTotal = max($sourceTotal, (int) data_get($meta, 'total', 0));

            if (!is_array($items) || count($items) === 0) {
                break;
            }

            foreach ($items as $item) {
                $country = $this->mapCountryPayload($item);

                if (!$country['country_code'] || !$country['name']) {
                    $skipped++;
                    continue;
                }

                $exists = DB::table('countries')->where('country_code', $country['country_code'])->exists();

                DB::table('countries')->updateOrInsert(
                    ['country_code' => $country['country_code']],
                    array_merge($country, [
                        'created_at' => $exists ? DB::raw('created_at') : $now,
                        'updated_at' => $now,
                    ])
                );

                $exists ? $updated++ : $inserted++;
            }

            $count = count($items);
            $offset += $limit;
            $more = data_get($meta, 'more');
        } while ($more !== null ? (bool) $more : ($count === $limit));

        $this->logApi('REST Countries API', $endpoint, $failed ? 'Partial' : 'Success', "source_total={$sourceTotal}, inserted={$inserted}, updated={$updated}, skipped={$skipped}, failed={$failed}", $now);

        return compact('endpoint', 'sourceTotal', 'inserted', 'updated', 'skipped', 'failed');
    }

    public function syncPorts(bool $all = true): array
    {
        $layerUrl = rtrim((string) config('services.world_port_index.layer_url'), '/');
        $queryEndpoint = $layerUrl . '/query';
        $timeout = max((int) config('services.external_api.timeout', 60), 120);
        $now = now();
        $inserted = 0;
        $updated = 0;
        $skipped = 0;
        $failedRecordCount = 0;
        $failedRequestCount = 0;
        $sourceCount = 0;
        $requestedCount = 0;
        $received = 0;
        $chunkSize = 100;
        $requestCount = 0;
        $layerMetadata = [];

        try {
            $metadataResponse = Http::timeout($timeout)->acceptJson()->get($layerUrl, [
                'f' => 'json',
            ]);

            if (!$metadataResponse->successful()) {
                return $this->failedPortResult($layerUrl, $this->httpMessage($metadataResponse->status()), [
                    'source_count' => $sourceCount,
                    'requested_count' => $requestedCount,
                    'received_count' => $received,
                    'inserted_count' => $inserted,
                    'updated_count' => $updated,
                    'skipped_count' => $skipped,
                    'failed_record_count' => $failedRecordCount,
                    'failed_request_count' => $failedRequestCount,
                    'chunk_size' => $chunkSize,
                    'request_count' => $requestCount,
                    'layer_metadata' => $layerMetadata,
                ], $now);
            }

            $layerMetadata = $metadataResponse->json() ?? [];
            $chunkSize = min((int) ($layerMetadata['maxRecordCount'] ?? 100), 100);
            $chunkSize = max($chunkSize, 1);

            $countResponse = Http::timeout($timeout)->acceptJson()->get($layerUrl . '/query', [
                'where' => '1=1',
                'returnCountOnly' => 'true',
                'f' => 'json',
            ]);

            if (!$countResponse->successful()) {
                return $this->failedPortResult($layerUrl, $this->httpMessage($countResponse->status()), [
                    'source_count' => $sourceCount,
                    'requested_count' => $requestedCount,
                    'received_count' => $received,
                    'inserted_count' => $inserted,
                    'updated_count' => $updated,
                    'skipped_count' => $skipped,
                    'failed_record_count' => $failedRecordCount,
                    'failed_request_count' => $failedRequestCount,
                    'chunk_size' => $chunkSize,
                    'request_count' => $requestCount,
                    'layer_metadata' => $layerMetadata,
                ], $now);
            }

            $sourceCount = (int) $countResponse->json('count', 0);

            $idsResponse = Http::timeout($timeout)->acceptJson()->get($layerUrl . '/query', [
                'where' => '1=1',
                'returnIdsOnly' => 'true',
                'f' => 'json',
            ]);

            if (!$idsResponse->successful()) {
                return $this->failedPortResult($layerUrl, $this->httpMessage($idsResponse->status()), [
                    'source_count' => $sourceCount,
                    'requested_count' => $requestedCount,
                    'received_count' => $received,
                    'inserted_count' => $inserted,
                    'updated_count' => $updated,
                    'skipped_count' => $skipped,
                    'failed_record_count' => $failedRecordCount,
                    'failed_request_count' => $failedRequestCount,
                    'chunk_size' => $chunkSize,
                    'request_count' => $requestCount,
                    'layer_metadata' => $layerMetadata,
                ], $now);
            }

            $objectIds = $idsResponse->json('objectIds') ?? [];

            if (!is_array($objectIds)) {
                $objectIds = [];
            }

            $objectIds = collect($objectIds)
                ->filter(fn ($id) => is_numeric($id))
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->sort()
                ->values();

            $sourceCount = max($sourceCount, $objectIds->count());

            foreach ($objectIds->chunk($chunkSize) as $index => $chunk) {
                $features = $this->fetchWorldPortChunk(
                    $queryEndpoint,
                    $chunk->values()->all(),
                    $index + 1,
                    $timeout,
                    $requestedCount,
                    $failedRequestCount,
                    $requestCount,
                    $now
                );

                $received += count($features);

                foreach ($features as $feature) {
                    try {
                        $saved = DB::transaction(fn () => $this->savePortFeature($feature, $now));

                        match ($saved) {
                            'inserted' => $inserted++,
                            'updated' => $updated++,
                            'skipped' => $skipped++,
                            default => $failedRecordCount++,
                        };
                    } catch (Throwable $exception) {
                        $failedRecordCount++;
                        $this->logApi('World Port Index', $layerUrl, 'Partial', 'Record gagal disimpan: ' . $this->externalErrorMessage($exception), $now);
                    }
                }
            }
        } catch (Throwable $exception) {
            $failedRequestCount++;
            $this->logApi('World Port Index', $layerUrl, 'Failed', $this->externalErrorMessage($exception), $now);
        }

        $message = "source_count={$sourceCount}, requested_count={$requestedCount}, received_count={$received}, inserted_count={$inserted}, updated_count={$updated}, skipped_count={$skipped}, failed_record_count={$failedRecordCount}, failed_request_count={$failedRequestCount}, chunk_size={$chunkSize}, request_count={$requestCount}";
        $this->logApi('World Port Index', $layerUrl, ($failedRecordCount || $failedRequestCount) ? 'Partial' : 'Success', $message, $now);

        return $this->portResult($layerUrl, [
            'source_count' => $sourceCount,
            'requested_count' => $requestedCount,
            'received_count' => $received,
            'inserted_count' => $inserted,
            'updated_count' => $updated,
            'skipped_count' => $skipped,
            'failed_record_count' => $failedRecordCount,
            'failed_request_count' => $failedRequestCount,
            'chunk_size' => $chunkSize,
            'request_count' => $requestCount,
            'layer_metadata' => $layerMetadata,
        ]);
    }

    public function backfillGlobalNews(int $pages = 3): array
    {
        return $this->syncNewsPages(collect([null]), $pages, true);
    }

    public function syncWatchlistNews(Collection $countries, int $pages = 1): array
    {
        return $this->syncNewsPages($countries, $pages, false);
    }

    private function syncNewsPages(Collection $countries, int $pages, bool $global): array
    {
        $endpoint = (string) config('services.gnews.search_url', 'https://gnews.io/api/v4/search');
        $apiKey = config('services.gnews.api_key');
        $timeout = (int) config('services.external_api.timeout', 30);
        $max = max(1, min((int) config('services.gnews.max_articles', 10), 10));
        $lang = config('services.gnews.lang', 'en');
        $inserted = 0;
        $duplicates = 0;
        $updated = 0;
        $failed = 0;
        $fetched = 0;
        $now = now();

        if (!$apiKey) {
            $this->logApi('GNews API', $endpoint, 'Failed', 'GNEWS_API_KEY belum diisi.', $now);

            return compact('endpoint', 'fetched', 'inserted', 'updated', 'duplicates', 'failed');
        }

        foreach ($countries as $country) {
            for ($page = 1; $page <= max(1, $pages); $page++) {
                $query = $global
                    ? 'supply chain OR logistics OR shipping OR trade OR freight OR "port congestion" OR export OR import'
                    : $country->name . ' supply chain OR logistics OR shipping OR trade OR freight OR port congestion OR export OR import';

                try {
                    $response = Http::timeout($timeout)->acceptJson()->get($endpoint, [
                        'q' => $query,
                        'lang' => $lang,
                        'max' => $max,
                        'page' => $page,
                        'sortby' => 'publishedAt',
                        'apikey' => $apiKey,
                    ]);
                } catch (Throwable $exception) {
                    $failed++;
                    $this->logApi('GNews API', $endpoint, 'Failed', $this->externalErrorMessage($exception), $now);
                    continue;
                }

                if (!$response->successful()) {
                    $failed++;
                    $this->logApi('GNews API', $endpoint, 'Failed', $this->httpMessage($response->status()), $now);
                    continue;
                }

                $articles = $response->json('articles') ?? [];
                $fetched += is_array($articles) ? count($articles) : 0;

                foreach ($articles as $article) {
                    $saved = $this->saveNewsArticle($article, $country->id ?? null, $now);
                    $$saved++;
                }
            }
        }

        $this->logApi('GNews API', $endpoint, $failed ? 'Partial' : 'Success', "mode=" . ($global ? 'global_backfill' : 'watchlist_incremental') . ", fetched={$fetched}, inserted={$inserted}, updated={$updated}, duplicates={$duplicates}, failed={$failed}", $now);

        return compact('endpoint', 'fetched', 'inserted', 'updated', 'duplicates', 'failed');
    }

    private function fetchWorldPortChunk(
        string $queryEndpoint,
        array $objectIds,
        int $chunkNumber,
        int $timeout,
        int &$requestedCount,
        int &$failedRequestCount,
        int &$requestCount,
        $now
    ): array {
        if ($objectIds === []) {
            return [];
        }

        $requestCount++;

        try {
            $response = Http::asForm()
                ->timeout($timeout)
                ->retry(3, 1500)
                ->post($queryEndpoint, [
                    'objectIds' => implode(',', $objectIds),
                    'outFields' => '*',
                    'returnGeometry' => 'true',
                    'outSR' => '4326',
                    'f' => 'json',
                ]);
        } catch (Throwable $exception) {
            $requestedCount += count($objectIds);
            $failedRequestCount++;
            $this->logApi('World Port Index', $queryEndpoint, 'Partial', "Chunk {$chunkNumber} gagal request. ids=" . count($objectIds) . ', error=' . $this->externalErrorMessage($exception), $now);

            return [];
        }

        if (!$response->successful()) {
            $requestedCount += count($objectIds);
            $failedRequestCount++;
            $this->logApi('World Port Index', $queryEndpoint, 'Partial', "Chunk {$chunkNumber} gagal HTTP. ids=" . count($objectIds) . ', status=' . $response->status() . ', message=' . $this->httpMessage($response->status()), $now);

            return [];
        }

        try {
            $payload = $response->json() ?? [];
        } catch (Throwable $exception) {
            $requestedCount += count($objectIds);
            $failedRequestCount++;
            $this->logApi('World Port Index', $queryEndpoint, 'Partial', "Chunk {$chunkNumber} JSON tidak valid. ids=" . count($objectIds) . ', status=' . $response->status(), $now);

            return [];
        }

        $arcgisError = $payload['error'] ?? null;

        if (is_array($arcgisError)) {
            $requestedCount += count($objectIds);
            $failedRequestCount++;
            $this->logApi('World Port Index', $queryEndpoint, 'Partial', "Chunk {$chunkNumber} error ArcGIS. ids=" . count($objectIds) . ', status=' . $response->status() . ', code=' . ($arcgisError['code'] ?? '-') . ', message=' . ($arcgisError['message'] ?? '-'), $now);

            return [];
        }

        if (($payload['exceededTransferLimit'] ?? false) === true && count($objectIds) > 1) {
            $half = max(1, (int) ceil(count($objectIds) / 2));
            $features = [];

            foreach (array_chunk($objectIds, $half) as $splitIndex => $splitIds) {
                $features = array_merge($features, $this->fetchWorldPortChunk(
                    $queryEndpoint,
                    $splitIds,
                    ($chunkNumber * 1000) + $splitIndex + 1,
                    $timeout,
                    $requestedCount,
                    $failedRequestCount,
                    $requestCount,
                    $now
                ));
            }

            return $features;
        }

        $requestedCount += count($objectIds);

        if (!array_key_exists('features', $payload) || !is_array($payload['features'])) {
            $failedRequestCount++;
            $this->logApi('World Port Index', $queryEndpoint, 'Partial', "Chunk {$chunkNumber} tidak memiliki key features. ids=" . count($objectIds) . ', status=' . $response->status(), $now);

            return [];
        }

        if ($payload['features'] === []) {
            $this->logApi('World Port Index', $queryEndpoint, 'Partial', "Chunk {$chunkNumber} features kosong. ids=" . count($objectIds) . ', status=' . $response->status() . ', arcgis_code=-, arcgis_message=-', $now);
        } elseif ($chunkNumber === 1) {
            $this->logApi('World Port Index', $queryEndpoint, 'Success', "Contoh chunk {$chunkNumber}: ids=" . count($objectIds) . ', features=' . count($payload['features']) . ', status=' . $response->status(), $now);
        }

        return $payload['features'];
    }

    private function savePortFeature(array $feature, $now): string
    {
        $attributes = array_change_key_case(data_get($feature, 'attributes', []), CASE_LOWER);
        $geometry = data_get($feature, 'geometry', []);
        $externalId = $this->firstValue($attributes, ['objectid', 'wpi_number', 'wpinumber', 'port_index_no', 'index_no', 'fid']);
        $name = $this->firstValue($attributes, ['port_name', 'portname', 'main_port_', 'main_port_name', 'regionname', 'name']);

        if (!$externalId || !$name) {
            return 'skipped';
        }

        $countryName = $this->firstValue($attributes, ['country', 'country_name', 'nation', 'countrycode']);
        $countryCode = $this->firstValue($attributes, ['countrycode', 'country_code', 'iso2', 'iso_a2']);
        $country = $this->findCountry($countryCode, $countryName);
        $lat = is_numeric(data_get($geometry, 'y')) ? (float) data_get($geometry, 'y') : null;
        $lng = is_numeric(data_get($geometry, 'x')) ? (float) data_get($geometry, 'x') : null;

        if ($lat === null || $lng === null || $lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) {
            return 'skipped';
        }

        $key = ['external_source' => 'world_port_index', 'external_id' => (string) $externalId];
        $exists = DB::table('ports')->where($key)->exists();

        DB::table('ports')->updateOrInsert($key, [
            'country_id' => $country->id ?? null,
            'name' => substr(trim($name), 0, 255),
            'city' => substr((string) ($this->firstValue($attributes, ['city', 'locality', 'municipality', 'town']) ?? ''), 0, 255) ?: null,
            'country_name' => $country->name ?? ($countryName ? substr($countryName, 0, 255) : null),
            'latitude' => round($lat, 7),
            'longitude' => round($lng, 7),
            'status' => 'Normal',
            'port_risk_score' => 20,
            'created_at' => $exists ? DB::raw('created_at') : $now,
            'updated_at' => $now,
        ]);

        return $exists ? 'updated' : 'inserted';
    }

    private function saveNewsArticle(array $article, ?int $countryId, $now): string
    {
        $url = trim((string) ($article['url'] ?? ''));

        if ($url === '') {
            return 'failed';
        }

        $hash = hash('sha256', $url);
        $exists = DB::table('news_cache')->where('url_hash', $hash)->orWhere('url', $url)->first();
        $publishedAt = $now;

        try {
            if (!empty($article['publishedAt'])) {
                $publishedAt = Carbon::parse($article['publishedAt']);
            }
        } catch (Throwable) {
            $publishedAt = $now;
        }

        $payload = [
            'country_id' => $countryId ?? ($exists->country_id ?? null),
            'title' => substr($this->normalizeText($article['title'] ?? null) ?? 'Tanpa judul', 0, 255),
            'description' => $this->normalizeText($article['description'] ?? null),
            'source' => $this->normalizeText(data_get($article, 'source.name')) ?? 'GNews',
            'url' => $url,
            'url_hash' => $hash,
            'image_url' => $this->normalizeText($article['image'] ?? null),
            'category' => 'GNews',
            'sentiment' => 'Neutral',
            'positive_score' => 0,
            'negative_score' => 0,
            'published_at' => $publishedAt,
            'updated_at' => $now,
        ];

        if ($exists) {
            DB::table('news_cache')->where('id', $exists->id)->update($payload);
            return 'duplicates';
        }

        DB::table('news_cache')->insert(array_merge($payload, ['created_at' => $now]));

        return 'inserted';
    }

    private function mapCountryPayload(array $item): array
    {
        $currencies = data_get($item, 'currencies', []);
        $currencyCode = is_array($currencies) ? array_key_first($currencies) : null;
        $currencyName = $currencyCode ? data_get($currencies, $currencyCode . '.name') : null;
        $languages = data_get($item, 'languages', []);
        $countryCode = $this->normalizeText(data_get($item, 'codes.alpha_2'));
        $countryName = $this->normalizeText(data_get($item, 'names.common'));
        $capital = $this->normalizeText(data_get($item, 'capitals.0.name', data_get($item, 'capitals.0')));
        $region = $this->normalizeText(data_get($item, 'region'));
        $currencyCode = $this->normalizeText($currencyCode);
        $currencyName = $this->normalizeText($currencyName);
        $language = $this->normalizeText($languages);

        return [
            'country_code' => $countryCode ? strtoupper(substr($countryCode, 0, 10)) : null,
            'name' => $countryName ? substr($countryName, 0, 255) : null,
            'capital' => $capital ? substr($capital, 0, 255) : null,
            'region' => $region ? substr($region, 0, 255) : null,
            'currency_code' => $currencyCode ? strtoupper(substr($currencyCode, 0, 10)) : null,
            'currency_name' => $currencyName ? substr($currencyName, 0, 255) : null,
            'language' => $language ? substr($language, 0, 255) : null,
            'latitude' => is_numeric(data_get($item, 'coordinates.lat')) ? data_get($item, 'coordinates.lat') : null,
            'longitude' => is_numeric(data_get($item, 'coordinates.lng')) ? data_get($item, 'coordinates.lng') : null,
        ];
    }

    private function normalizeText(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (is_string($value)) {
            $value = trim($value);

            return $value !== '' ? $value : null;
        }

        if (is_int($value) || is_float($value) || is_bool($value)) {
            return (string) $value;
        }

        if (is_array($value)) {
            $values = [];

            array_walk_recursive($value, function ($item) use (&$values) {
                if (
                    is_string($item)
                    || is_int($item)
                    || is_float($item)
                ) {
                    $item = trim((string) $item);

                    if ($item !== '') {
                        $values[] = $item;
                    }
                }
            });

            $values = array_values(array_unique($values));

            return $values !== []
                ? implode(', ', $values)
                : null;
        }

        return null;
    }

    private function findCountry(?string $code, ?string $name): ?object
    {
        if ($code) {
            $country = DB::table('countries')->where('country_code', strtoupper(trim($code)))->first();
            if ($country) {
                return $country;
            }
        }

        return $name ? DB::table('countries')->where('name', trim($name))->first() : null;
    }

    private function firstValue(array $attributes, array $keys): ?string
    {
        foreach ($keys as $key) {
            $value = $attributes[strtolower($key)] ?? null;
            $value = $this->normalizeText($value);

            if ($value !== null) {
                return $value;
            }
        }

        return null;
    }

    private function failedPortResult(string $layerUrl, string $message, array $counts, $now): array
    {
        $counts['failed_request_count'] = ($counts['failed_request_count'] ?? 0) + 1;
        $this->logApi('World Port Index', $layerUrl, 'Failed', $message, $now);

        return $this->portResult($layerUrl, $counts);
    }

    private function portResult(string $layerUrl, array $counts): array
    {
        $sourceCount = (int) ($counts['source_count'] ?? 0);
        $requestedCount = (int) ($counts['requested_count'] ?? 0);
        $receivedCount = (int) ($counts['received_count'] ?? 0);
        $insertedCount = (int) ($counts['inserted_count'] ?? 0);
        $updatedCount = (int) ($counts['updated_count'] ?? 0);
        $skippedCount = (int) ($counts['skipped_count'] ?? 0);
        $failedRecordCount = (int) ($counts['failed_record_count'] ?? 0);
        $failedRequestCount = (int) ($counts['failed_request_count'] ?? 0);
        $chunkSize = (int) ($counts['chunk_size'] ?? 100);
        $requestCount = (int) ($counts['request_count'] ?? 0);

        return array_merge($counts, [
            'layerUrl' => $layerUrl,
            'source_count' => $sourceCount,
            'requested_count' => $requestedCount,
            'received_count' => $receivedCount,
            'inserted_count' => $insertedCount,
            'updated_count' => $updatedCount,
            'skipped_count' => $skippedCount,
            'failed_record_count' => $failedRecordCount,
            'failed_request_count' => $failedRequestCount,
            'chunk_size' => $chunkSize,
            'request_count' => $requestCount,
            'sourceTotal' => $sourceCount,
            'received' => $receivedCount,
            'inserted' => $insertedCount,
            'updated' => $updatedCount,
            'skipped' => $skippedCount,
            'failed' => $failedRecordCount + $failedRequestCount,
        ]);
    }

    private function logApi(string $apiName, ?string $endpoint, string $status, string $message, $timestamp): void
    {
        DB::table('api_logs')->insert([
            'api_name' => $apiName,
            'endpoint' => $endpoint ? preg_replace('/([?&](?:apikey|api_key|key|token)=)[^&]+/i', '$1[hidden]', $endpoint) : null,
            'status' => $status,
            'message' => substr($message, 0, 1000),
            'requested_at' => $timestamp,
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ]);
    }

    private function httpMessage(int $status): string
    {
        return match ($status) {
            401, 403 => 'API key tidak valid, kedaluwarsa, atau tidak memiliki izin.',
            429 => 'Kuota API mencapai batas.',
            default => 'HTTP status: ' . $status,
        };
    }

    private function externalErrorMessage(Throwable $exception): string
    {
        $message = $exception->getMessage();

        return str_contains(strtolower($message), 'timed out') || str_contains(strtolower($message), 'timeout')
            ? 'Request timeout.'
            : $message;
    }
}
