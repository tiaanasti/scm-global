<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Throwable;

class PortRiskController extends Controller
{
    public function recalculate()
    {
        $countries = DB::table('countries')
            ->select('id', 'name')
            ->get();

        $updatedPorts = 0;
        $countriesWithoutPorts = 0;
        $now = now();

        try {
            foreach ($countries as $country) {
                $ports = DB::table('ports')
                    ->where('country_id', $country->id)
                    ->get();

                if ($ports->isEmpty()) {
                    $countriesWithoutPorts++;
                    continue;
                }

                $risk = DB::table('risk_scores')
                    ->where('country_id', $country->id)
                    ->orderByDesc('score_date')
                    ->first();

                $weather = DB::table('weather_reports')
                    ->where('country_id', $country->id)
                    ->orderByDesc('reported_at')
                    ->first();

                $newsItems = DB::table('news_cache')
                    ->where('country_id', $country->id)
                    ->get();

                $countryRiskScore = min(
                    max((float) ($risk->total_score ?? 0), 0),
                    100
                );

                $weatherRiskScore = min(
                    max((float) ($weather->weather_risk_score ?? 0), 0),
                    100
                );

                $negativeNews = $newsItems
                    ->where('sentiment', 'Negative')
                    ->count();

                $positiveNews = $newsItems
                    ->where('sentiment', 'Positive')
                    ->count();

                $neutralNews = $newsItems
                    ->where('sentiment', 'Neutral')
                    ->count();

                $totalNews = $negativeNews
                    + $positiveNews
                    + $neutralNews;

                if ($totalNews === 0) {
                    $newsRiskScore = 30;
                } else {
                    $negativePercentage =
                        ($negativeNews / $totalNews) * 100;

                    $positivePercentage =
                        ($positiveNews / $totalNews) * 100;

                    $newsRiskScore = 30
                        + ($negativePercentage * 0.7)
                        - ($positivePercentage * 0.3);

                    $newsRiskScore = min(
                        max($newsRiskScore, 0),
                        100
                    );
                }

                $portRiskScore = (int) round(
                    ($countryRiskScore * 0.50)
                    + ($weatherRiskScore * 0.30)
                    + ($newsRiskScore * 0.20)
                );

                if ($portRiskScore >= 60) {
                    $status = 'Siaga';
                } elseif ($portRiskScore >= 35) {
                    $status = 'Waspada';
                } else {
                    $status = 'Aman';
                }

                DB::table('ports')
                    ->where('country_id', $country->id)
                    ->update([
                        'status' => $status,
                        'port_risk_score' => $portRiskScore,
                        'updated_at' => $now,
                    ]);

                $updatedPorts += $ports->count();
            }

            $message =
                'Risk scoring pelabuhan selesai. Pelabuhan diperbarui: '
                . $updatedPorts
                . ', negara tanpa pelabuhan: '
                . $countriesWithoutPorts
                . '.';

            DB::table('api_logs')->insert([
                'api_name' => 'Port Risk Scoring',
                'endpoint' => 'internal calculation',
                'status' => 'Success',
                'message' => $message,
                'requested_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            return redirect()
                ->route('admin.index')
                ->with('success', $message);
        } catch (Throwable $exception) {
            DB::table('api_logs')->insert([
                'api_name' => 'Port Risk Scoring',
                'endpoint' => 'internal calculation',
                'status' => 'Failed',
                'message' => substr(
                    $exception->getMessage(),
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
                    'Perhitungan risiko pelabuhan gagal: '
                    . $exception->getMessage()
                );
        }
    }
}