<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HistoricalTrendSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $history = [
            'ID' => [
                'currency' => [
                    ['rate_date' => '2026-01-20', 'exchange_rate' => 15680, 'change_percentage' => 0.45, 'currency_risk_score' => 35],
                    ['rate_date' => '2026-02-20', 'exchange_rate' => 15820, 'change_percentage' => 0.89, 'currency_risk_score' => 38],
                    ['rate_date' => '2026-03-20', 'exchange_rate' => 15950, 'change_percentage' => 0.82, 'currency_risk_score' => 40],
                    ['rate_date' => '2026-04-20', 'exchange_rate' => 16010, 'change_percentage' => 0.38, 'currency_risk_score' => 36],
                    ['rate_date' => '2026-05-20', 'exchange_rate' => 16120, 'change_percentage' => 0.69, 'currency_risk_score' => 38],
                    ['rate_date' => '2026-06-20', 'exchange_rate' => 16180, 'change_percentage' => 0.37, 'currency_risk_score' => 35],
                    ['rate_date' => '2026-07-10', 'exchange_rate' => 16210, 'change_percentage' => 0.19, 'currency_risk_score' => 34],
                ],
                'risk' => [
                    ['score_date' => '2026-01-20', 'total_score' => 38, 'weather_score' => 40, 'inflation_score' => 20, 'currency_score' => 35, 'news_score' => 25, 'risk_level' => 'Risiko Sedang'],
                    ['score_date' => '2026-02-20', 'total_score' => 40, 'weather_score' => 42, 'inflation_score' => 20, 'currency_score' => 38, 'news_score' => 28, 'risk_level' => 'Risiko Sedang'],
                    ['score_date' => '2026-03-20', 'total_score' => 41, 'weather_score' => 43, 'inflation_score' => 20, 'currency_score' => 40, 'news_score' => 28, 'risk_level' => 'Risiko Sedang'],
                    ['score_date' => '2026-04-20', 'total_score' => 39, 'weather_score' => 41, 'inflation_score' => 20, 'currency_score' => 36, 'news_score' => 25, 'risk_level' => 'Risiko Sedang'],
                    ['score_date' => '2026-05-20', 'total_score' => 43, 'weather_score' => 44, 'inflation_score' => 20, 'currency_score' => 38, 'news_score' => 30, 'risk_level' => 'Risiko Sedang'],
                    ['score_date' => '2026-06-20', 'total_score' => 44, 'weather_score' => 45, 'inflation_score' => 20, 'currency_score' => 38, 'news_score' => 30, 'risk_level' => 'Risiko Sedang'],
                    ['score_date' => '2026-07-10', 'total_score' => 42, 'weather_score' => 45, 'inflation_score' => 20, 'currency_score' => 38, 'news_score' => 25, 'risk_level' => 'Risiko Sedang'],
                ],
            ],
            'CN' => [
                'currency' => [
                    ['rate_date' => '2026-01-20', 'exchange_rate' => 7.18, 'change_percentage' => 0.22, 'currency_risk_score' => 45],
                    ['rate_date' => '2026-02-20', 'exchange_rate' => 7.19, 'change_percentage' => 0.14, 'currency_risk_score' => 46],
                    ['rate_date' => '2026-03-20', 'exchange_rate' => 7.20, 'change_percentage' => 0.14, 'currency_risk_score' => 48],
                    ['rate_date' => '2026-04-20', 'exchange_rate' => 7.22, 'change_percentage' => 0.28, 'currency_risk_score' => 49],
                    ['rate_date' => '2026-05-20', 'exchange_rate' => 7.21, 'change_percentage' => -0.14, 'currency_risk_score' => 48],
                    ['rate_date' => '2026-06-20', 'exchange_rate' => 7.20, 'change_percentage' => -0.14, 'currency_risk_score' => 47],
                ],
                'risk' => [
                    ['score_date' => '2026-01-20', 'total_score' => 60, 'weather_score' => 55, 'inflation_score' => 18, 'currency_score' => 48, 'news_score' => 65, 'risk_level' => 'Risiko Tinggi'],
                    ['score_date' => '2026-02-20', 'total_score' => 62, 'weather_score' => 58, 'inflation_score' => 18, 'currency_score' => 49, 'news_score' => 68, 'risk_level' => 'Risiko Tinggi'],
                    ['score_date' => '2026-03-20', 'total_score' => 64, 'weather_score' => 60, 'inflation_score' => 18, 'currency_score' => 50, 'news_score' => 70, 'risk_level' => 'Risiko Tinggi'],
                    ['score_date' => '2026-04-20', 'total_score' => 63, 'weather_score' => 59, 'inflation_score' => 18, 'currency_score' => 49, 'news_score' => 69, 'risk_level' => 'Risiko Tinggi'],
                    ['score_date' => '2026-05-20', 'total_score' => 65, 'weather_score' => 60, 'inflation_score' => 18, 'currency_score' => 50, 'news_score' => 70, 'risk_level' => 'Risiko Tinggi'],
                    ['score_date' => '2026-06-20', 'total_score' => 65, 'weather_score' => 60, 'inflation_score' => 18, 'currency_score' => 50, 'news_score' => 70, 'risk_level' => 'Risiko Tinggi'],
                ],
            ],
        ];

        foreach ($history as $countryCode => $rows) {
            $country = DB::table('countries')
                ->where('country_code', $countryCode)
                ->first();

            if (!$country) {
                continue;
            }

            foreach ($rows['currency'] as $row) {
                $exists = DB::table('currency_rates')
                    ->where('country_id', $country->id)
                    ->where('rate_date', $row['rate_date'])
                    ->exists();

                if ($exists) {
                    continue;
                }

                DB::table('currency_rates')->insert([
                    'country_id' => $country->id,
                    'base_currency' => 'USD',
                    'target_currency' => $country->currency_code,
                    'exchange_rate' => $row['exchange_rate'],
                    'change_percentage' => $row['change_percentage'],
                    'currency_risk_score' => $row['currency_risk_score'],
                    'rate_date' => $row['rate_date'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            foreach ($rows['risk'] as $row) {
                $exists = DB::table('risk_scores')
                    ->where('country_id', $country->id)
                    ->where('score_date', $row['score_date'])
                    ->exists();

                if ($exists) {
                    continue;
                }

                DB::table('risk_scores')->insert([
                    'country_id' => $country->id,
                    'weather_score' => $row['weather_score'],
                    'inflation_score' => $row['inflation_score'],
                    'currency_score' => $row['currency_score'],
                    'news_score' => $row['news_score'],
                    'total_score' => $row['total_score'],
                    'risk_level' => $row['risk_level'],
                    'recommendation' => 'Data historis skor risiko tersimpan untuk analisis tren.',
                    'score_date' => $row['score_date'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }
}
