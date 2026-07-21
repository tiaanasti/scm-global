<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Throwable;

class SupplyChainSyncService
{
    public function watchlistedCountriesForUser(?int $userId): Collection
    {
        if (!$userId) {
            return collect();
        }

        return DB::table('countries')
            ->join('watchlists', 'countries.id', '=', 'watchlists.country_id')
            ->where('watchlists.user_id', $userId)
            ->select('countries.*')
            ->distinct()
            ->orderBy('countries.name')
            ->get();
    }

    public function monitoredCountries(): Collection
    {
        $countries = DB::table('countries')
            ->join('watchlists', 'countries.id', '=', 'watchlists.country_id')
            ->select('countries.*')
            ->groupBy(
                'countries.id',
                'countries.country_code',
                'countries.name',
                'countries.capital',
                'countries.region',
                'countries.currency_code',
                'countries.currency_name',
                'countries.language',
                'countries.latitude',
                'countries.longitude',
                'countries.created_at',
                'countries.updated_at'
            )
            ->orderBy('countries.name')
            ->get();

        if ($countries->isNotEmpty()) {
            return $countries;
        }

        $defaultCountry = DB::table('countries')
            ->where('country_code', 'ID')
            ->first()
            ?? DB::table('countries')->orderBy('name')->first();

        return $defaultCountry ? collect([$defaultCountry]) : collect();
    }

    public function syncWeather(?Collection $countries = null): array
    {
        $countries = $countries ?? $this->monitoredCountries();
        $endpoint = config('services.open_meteo.forecast_url');
        $timeout = (int) config('services.external_api.timeout', 15);
        $result = $this->emptyResult($countries->count());

        foreach ($countries as $country) {
            $now = now();

            if (!$country->latitude || !$country->longitude) {
                $this->logApi('Open-Meteo', $endpoint, 'Failed', 'Koordinat latitude/longitude untuk ' . $country->name . ' belum tersedia.', $now);
                $result['failed']++;
                continue;
            }

            try {
                $response = Http::timeout($timeout)->get($endpoint, [
                    'latitude' => $country->latitude,
                    'longitude' => $country->longitude,
                    'current' => 'temperature_2m,precipitation,wind_speed_10m',
                    'timezone' => 'auto',
                ]);

                if (!$response->successful()) {
                    $this->logApi('Open-Meteo', $endpoint, 'Failed', 'Gagal mengambil data cuaca ' . $country->name . '. ' . $this->httpMessage($response->status()), $now);
                    $result['failed']++;
                    continue;
                }

                $current = $response->json('current') ?? [];
                $temperature = $current['temperature_2m'] ?? 0;
                $rainfall = $current['precipitation'] ?? 0;
                $windSpeed = $current['wind_speed_10m'] ?? 0;

                $weatherStatus = 'Normal';
                $weatherRiskScore = 20;

                if ($rainfall >= 15 || $windSpeed >= 25) {
                    $weatherStatus = 'Cuaca Berisiko';
                    $weatherRiskScore = 75;
                } elseif ($rainfall >= 5 || $windSpeed >= 15) {
                    $weatherStatus = 'Waspada';
                    $weatherRiskScore = 45;
                }

                DB::table('weather_reports')->insert([
                    'country_id' => $country->id,
                    'temperature' => $temperature,
                    'rainfall' => $rainfall,
                    'wind_speed' => $windSpeed,
                    'weather_status' => $weatherStatus,
                    'weather_risk_score' => $weatherRiskScore,
                    'reported_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                $this->logApi('Open-Meteo', $endpoint, 'Success', 'Data cuaca ' . $country->name . ' berhasil diperbarui.', $now);
                $result['success']++;
            } catch (Throwable $exception) {
                $this->logApi('Open-Meteo', $endpoint, 'Failed', $country->name . ': ' . $this->exceptionMessage($exception), $now);
                $result['failed']++;
            }
        }

        return $result;
    }

    public function syncCurrency(?Collection $countries = null): array
    {
        $countries = $countries ?? $this->monitoredCountries();
        $endpoint = config('services.exchange_rate.latest_url');
        $timeout = (int) config('services.external_api.timeout', 15);
        $result = $this->emptyResult($countries->count());

        foreach ($countries as $country) {
            $now = now();

            if (!$country->currency_code) {
                $this->logApi('ExchangeRate-API', $endpoint, 'Failed', 'Kode mata uang untuk ' . $country->name . ' belum tersedia.', $now);
                $result['failed']++;
                continue;
            }

            try {
                $response = Http::timeout($timeout)->get($endpoint);

                if (!$response->successful()) {
                    $this->logApi('ExchangeRate-API', $endpoint, 'Failed', 'Gagal mengambil data kurs untuk ' . $country->name . '. ' . $this->httpMessage($response->status()), $now);
                    $result['failed']++;
                    continue;
                }

                $targetCurrency = strtoupper($country->currency_code);
                $exchangeRate = $response->json('rates.' . $targetCurrency);

                if (!$exchangeRate) {
                    $this->logApi('ExchangeRate-API', $endpoint, 'Failed', 'Kode mata uang ' . $targetCurrency . ' tidak ditemukan pada response API.', $now);
                    $result['failed']++;
                    continue;
                }

                $oldCurrency = DB::table('currency_rates')
                    ->where('country_id', $country->id)
                    ->latest('rate_date')
                    ->first();

                $oldRate = $oldCurrency->exchange_rate ?? $exchangeRate;
                $changePercentage = $oldRate > 0 ? (($exchangeRate - $oldRate) / $oldRate) * 100 : 0;

                $currencyRiskScore = 20;

                if (abs($changePercentage) >= 5) {
                    $currencyRiskScore = 80;
                } elseif (abs($changePercentage) >= 3) {
                    $currencyRiskScore = 60;
                } elseif (abs($changePercentage) >= 1) {
                    $currencyRiskScore = 40;
                }

                $this->storeCurrencyRateSnapshot($country->id, [
                    'base_currency' => 'USD',
                    'target_currency' => $targetCurrency,
                    'exchange_rate' => $exchangeRate,
                    'change_percentage' => round($changePercentage, 2),
                    'currency_risk_score' => $currencyRiskScore,
                    'rate_date' => $now->toDateString(),
                ], $now);

                $this->logApi('ExchangeRate-API', $endpoint, 'Success', 'Data kurs ' . $targetCurrency . ' berhasil diperbarui.', $now);
                $result['success']++;
            } catch (Throwable $exception) {
                $this->logApi('ExchangeRate-API', $endpoint, 'Failed', $country->name . ': ' . $this->exceptionMessage($exception), $now);
                $result['failed']++;
            }
        }

        return $result;
    }

    public function syncNews(?Collection $countries = null): array
    {
        $countries = $countries ?? $this->monitoredCountries();
        $endpoint = config('services.gnews.search_url');
        $apiKey = config('services.gnews.api_key');
        $timeout = (int) config('services.external_api.timeout', 15);
        $maxArticles = (int) config('services.gnews.max_articles', 5);
        $lang = config('services.gnews.lang', 'en');
        $result = $this->emptyResult($countries->count());

        $positiveWords = DB::table('positive_words')->pluck('word')->map(fn ($word) => strtolower($word))->toArray();
        $negativeWords = DB::table('negative_words')->pluck('word')->map(fn ($word) => strtolower($word))->toArray();

        foreach ($countries as $country) {
            $now = now();

            if (!$apiKey) {
                $this->logApi('GNews API', $endpoint ?? 'GNews Search Endpoint', 'Failed', 'GNEWS_API_KEY belum diisi di file .env.', $now);
                $result['failed']++;
                continue;
            }

            try {
                $query = $country->name . ' supply chain OR logistics OR port OR import OR export OR inflation OR currency';
                $response = Http::timeout($timeout)->get($endpoint, [
                    'q' => $query,
                    'lang' => $lang,
                    'max' => $maxArticles,
                    'sortby' => 'publishedAt',
                    'apikey' => $apiKey,
                ]);

                if (!$response->successful()) {
                    $this->logApi('GNews API', $endpoint, 'Failed', 'Gagal mengambil berita untuk ' . $country->name . '. ' . $this->httpMessage($response->status()), $now);
                    $result['failed']++;
                    continue;
                }

                $articles = $response->json('articles') ?? [];

                foreach ($articles as $article) {
                    $title = $article['title'] ?? 'Tanpa judul';
                    $description = $article['description'] ?? '';
                    $url = $article['url'] ?? null;
                    $articleUrl = $url ?: 'gnews://' . md5($country->id . $title . ($article['publishedAt'] ?? ''));
                    $sourceName = $article['source']['name'] ?? 'GNews';

                    try {
                        $publishedAt = isset($article['publishedAt'])
                            ? \Carbon\Carbon::parse($article['publishedAt'])->toDateTimeString()
                            : $now;
                    } catch (Throwable) {
                        $publishedAt = $now;
                    }

                    $textForSentiment = strtolower($title . ' ' . $description);
                    $positiveScore = 0;
                    $negativeScore = 0;

                    foreach ($positiveWords as $word) {
                        if ($word && str_contains($textForSentiment, $word)) {
                            $positiveScore++;
                        }
                    }

                    foreach ($negativeWords as $word) {
                        if ($word && str_contains($textForSentiment, $word)) {
                            $negativeScore++;
                        }
                    }

                    $sentiment = 'Neutral';

                    if ($negativeScore > $positiveScore) {
                        $sentiment = 'Negative';
                    } elseif ($positiveScore > $negativeScore) {
                        $sentiment = 'Positive';
                    }

                    DB::table('news_cache')->updateOrInsert(
                        ['url' => $articleUrl],
                        [
                            'country_id' => $country->id,
                            'title' => $title,
                            'description' => $description,
                            'source' => $sourceName,
                            'category' => 'GNews',
                            'sentiment' => $sentiment,
                            'positive_score' => $positiveScore,
                            'negative_score' => $negativeScore,
                            'published_at' => $publishedAt,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]
                    );
                }

                $this->logApi('GNews API', $endpoint, 'Success', 'Berita ' . $country->name . ' berhasil diperbarui. Total artikel: ' . count($articles) . '.', $now);
                $result['success']++;
            } catch (Throwable $exception) {
                $this->logApi('GNews API', $endpoint ?? 'GNews Search Endpoint', 'Failed', $country->name . ': ' . $this->exceptionMessage($exception), $now);
                $result['failed']++;
            }
        }

        return $result;
    }

    public function recalculateRisks(?Collection $countries = null): array
    {
        $countries = $countries ?? $this->monitoredCountries();
        $result = $this->emptyResult($countries->count());

        foreach ($countries as $country) {
            try {
                $economic = $this->latestEconomicForCountry($country->id);
                $weather = DB::table('weather_reports')->where('country_id', $country->id)->latest('reported_at')->first();
                $currency = DB::table('currency_rates')->where('country_id', $country->id)->latest('rate_date')->first();
                $newsItems = DB::table('news_cache')->where('country_id', $country->id)->get();

                $weatherScore = 0;

                if ($weather) {
                    $weatherScore = $weather->weather_risk_score ?? 0;

                    if (($weather->rainfall ?? 0) >= 15) {
                        $weatherScore += 15;
                    }

                    if (($weather->wind_speed ?? 0) >= 25) {
                        $weatherScore += 15;
                    }

                    if (($weather->temperature ?? 0) >= 35) {
                        $weatherScore += 10;
                    }
                }

                $weatherScore = min($weatherScore, 100);
                $inflationRate = $economic->inflation_rate ?? 0;

                if ($inflationRate < 3) {
                    $inflationScore = 20;
                } elseif ($inflationRate < 6) {
                    $inflationScore = 45;
                } elseif ($inflationRate < 10) {
                    $inflationScore = 70;
                } else {
                    $inflationScore = 90;
                }

                $changePercentage = abs($currency->change_percentage ?? 0);

                if ($changePercentage < 1) {
                    $currencyScore = 20;
                } elseif ($changePercentage < 3) {
                    $currencyScore = 45;
                } elseif ($changePercentage < 5) {
                    $currencyScore = 70;
                } else {
                    $currencyScore = 90;
                }

                $negativeNews = $newsItems->where('sentiment', 'Negative')->count();
                $positiveNews = $newsItems->where('sentiment', 'Positive')->count();
                $newsScore = 30;

                if ($negativeNews > $positiveNews) {
                    $newsScore = 70;
                }

                if ($negativeNews >= 3) {
                    $newsScore = 90;
                }

                if ($positiveNews > $negativeNews) {
                    $newsScore = 20;
                }

                $totalScore = round(($weatherScore * 0.25) + ($inflationScore * 0.30) + ($currencyScore * 0.25) + ($newsScore * 0.20));

                if ($totalScore >= 60) {
                    $riskLevel = 'Risiko Tinggi';
                    $recommendation = 'Negara ini memiliki risiko rantai pasok yang cukup tinggi. Perlu dilakukan pemantauan cuaca, kurs, inflasi, dan berita sebelum mengambil keputusan impor.';
                } elseif ($totalScore >= 35) {
                    $riskLevel = 'Risiko Sedang';
                    $recommendation = 'Negara ini masih dapat dipertimbangkan sebagai sumber impor, tetapi tetap perlu pemantauan berkala terhadap indikator risiko utama.';
                } else {
                    $riskLevel = 'Risiko Rendah';
                    $recommendation = 'Negara ini relatif aman untuk aktivitas rantai pasok dan dapat menjadi pilihan impor yang cukup stabil.';
                }

                $this->storeRiskScoreSnapshot($country->id, [
                    'weather_score' => $weatherScore,
                    'inflation_score' => $inflationScore,
                    'currency_score' => $currencyScore,
                    'news_score' => $newsScore,
                    'total_score' => $totalScore,
                    'risk_level' => $riskLevel,
                    'recommendation' => $recommendation,
                    'score_date' => now()->toDateString(),
                ]);

                $result['success']++;
            } catch (Throwable $exception) {
                $this->logApi('Risk Scoring', null, 'Failed', $country->name . ': ' . $this->exceptionMessage($exception), now());
                $result['failed']++;
            }
        }

        $this->logApi('Risk Scoring', null, $result['failed'] > 0 ? 'Partial' : 'Success', 'Perhitungan risiko selesai. Negara diproses: ' . $result['success'] . ', gagal: ' . $result['failed'] . '.', now());

        return $result;
    }

    public function syncExternalApisForCountries(Collection $countries): array
    {
        $weather = $this->syncWeather($countries);
        $currency = $this->syncCurrency($countries);
        $news = $this->syncNews($countries);
        $risk = $this->recalculateRisks($countries);

        return [
            'countries' => $countries->count(),
            'success' => $weather['success'] + $currency['success'] + $news['success'] + $risk['success'],
            'failed' => $weather['failed'] + $currency['failed'] + $news['failed'] + $risk['failed'],
            'weather' => $weather,
            'currency' => $currency,
            'news' => $news,
            'risk' => $risk,
        ];
    }

    private function latestEconomicForCountry($countryId)
    {
        if (!$countryId) {
            return null;
        }

        return DB::table('economic_indicators')
            ->where('country_id', $countryId)
            ->where(function ($query) {
                $query->where('gdp', '>', 0)
                    ->orWhere('exports', '>', 0)
                    ->orWhere('imports', '>', 0)
                    ->orWhere('inflation_rate', '<>', 0);
            })
            ->orderByDesc('year')
            ->first()
            ?? DB::table('economic_indicators')
                ->where('country_id', $countryId)
                ->orderByDesc('year')
                ->first();
    }

    private function storeCurrencyRateSnapshot(int $countryId, array $data, $timestamp = null): void
    {
        $timestamp = $timestamp ?? now();
        $rateDate = $data['rate_date'] ?? $timestamp->toDateString();

        $payload = [
            'base_currency' => $data['base_currency'] ?? 'USD',
            'target_currency' => $data['target_currency'],
            'exchange_rate' => $data['exchange_rate'],
            'change_percentage' => $data['change_percentage'] ?? 0,
            'currency_risk_score' => $data['currency_risk_score'] ?? 0,
            'rate_date' => $rateDate,
            'updated_at' => $timestamp,
        ];

        $existing = DB::table('currency_rates')
            ->where('country_id', $countryId)
            ->where('rate_date', $rateDate)
            ->first();

        if ($existing) {
            DB::table('currency_rates')->where('id', $existing->id)->update($payload);
            return;
        }

        DB::table('currency_rates')->insert(array_merge($payload, [
            'country_id' => $countryId,
            'created_at' => $timestamp,
        ]));
    }

    private function storeRiskScoreSnapshot(int $countryId, array $data, $timestamp = null): void
    {
        $timestamp = $timestamp ?? now();
        $scoreDate = $data['score_date'] ?? $timestamp->toDateString();

        $payload = [
            'weather_score' => $data['weather_score'] ?? 0,
            'inflation_score' => $data['inflation_score'] ?? 0,
            'currency_score' => $data['currency_score'] ?? 0,
            'news_score' => $data['news_score'] ?? 0,
            'total_score' => $data['total_score'] ?? 0,
            'risk_level' => $data['risk_level'] ?? 'Risiko Rendah',
            'recommendation' => $data['recommendation'] ?? null,
            'score_date' => $scoreDate,
            'updated_at' => $timestamp,
        ];

        $existing = DB::table('risk_scores')
            ->where('country_id', $countryId)
            ->where('score_date', $scoreDate)
            ->first();

        if ($existing) {
            DB::table('risk_scores')->where('id', $existing->id)->update($payload);
            return;
        }

        DB::table('risk_scores')->insert(array_merge($payload, [
            'country_id' => $countryId,
            'created_at' => $timestamp,
        ]));
    }

    private function emptyResult(int $countries): array
    {
        return [
            'countries' => $countries,
            'success' => 0,
            'failed' => 0,
        ];
    }

    private function logApi(string $apiName, ?string $endpoint, string $status, string $message, $timestamp): void
    {
        DB::table('api_logs')->insert([
            'api_name' => $apiName,
            'endpoint' => $this->safeEndpoint($endpoint),
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
            429 => 'Kuota API telah mencapai batas.',
            default => 'HTTP status: ' . $status . '.',
        };
    }

    private function exceptionMessage(Throwable $exception): string
    {
        $message = $exception->getMessage();

        if (str_contains(strtolower($message), 'timed out') || str_contains(strtolower($message), 'timeout')) {
            return 'Koneksi API timeout dan akan dicoba kembali pada jadwal berikutnya.';
        }

        return $message;
    }

    private function safeEndpoint(?string $endpoint): ?string
    {
        if (!$endpoint) {
            return null;
        }

        return preg_replace('/([?&](?:apikey|api_key|key|token)=)[^&]+/i', '$1[hidden]', $endpoint);
    }
}
