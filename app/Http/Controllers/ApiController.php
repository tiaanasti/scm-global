<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{
    public function countries()
    {
        $countries = DB::table('countries')
            ->leftJoin('risk_scores', 'countries.id', '=', 'risk_scores.country_id')
            ->select(
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
                'risk_scores.total_score',
                'risk_scores.risk_level'
            )
            ->orderBy('countries.name')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Data negara berhasil diambil.',
            'data' => $countries
        ]);
    }

    public function risk(Request $request)
    {
        $countryId = $request->get('country_id');

        $query = DB::table('risk_scores')
            ->join('countries', 'risk_scores.country_id', '=', 'countries.id')
            ->select(
                'risk_scores.id',
                'countries.id as country_id',
                'countries.name as country_name',
                'countries.region',
                'risk_scores.weather_score',
                'risk_scores.inflation_score',
                'risk_scores.currency_score',
                'risk_scores.news_score',
                'risk_scores.total_score',
                'risk_scores.risk_level',
                'risk_scores.recommendation',
                'risk_scores.score_date'
            );

        if ($countryId) {
            $query->where('countries.id', $countryId);
        }

        $riskScores = $query
            ->orderByDesc('risk_scores.total_score')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Data risiko berhasil diambil.',
            'data' => $riskScores
        ]);
    }

    public function ports(Request $request)
    {
        $countryId = $request->get('country_id');

        $query = DB::table('ports')
            ->leftJoin('countries', 'ports.country_id', '=', 'countries.id')
            ->select(
                'ports.id',
                'ports.name',
                'ports.city',
                'ports.country_name',
                'countries.name as country_real_name',
                'ports.latitude',
                'ports.longitude',
                'ports.status',
                'ports.port_risk_score'
            );

        if ($countryId) {
            $query->where('ports.country_id', $countryId);
        }

        $ports = $query
            ->orderBy('ports.name')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Data pelabuhan berhasil diambil.',
            'data' => $ports
        ]);
    }

    public function news(Request $request)
    {
        $countryId = $request->get('country_id');

        $query = DB::table('news_cache')
            ->leftJoin('countries', 'news_cache.country_id', '=', 'countries.id')
            ->select(
                'news_cache.id',
                'countries.name as country_name',
                'news_cache.title',
                'news_cache.description',
                'news_cache.source',
                'news_cache.url',
                'news_cache.category',
                'news_cache.sentiment',
                'news_cache.positive_score',
                'news_cache.negative_score',
                'news_cache.published_at'
            );

        if ($countryId) {
            $query->where('news_cache.country_id', $countryId);
        }

        $news = $query
            ->latest('news_cache.published_at')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Data berita berhasil diambil.',
            'data' => $news
        ]);
    }

    public function currency(Request $request)
    {
        $countryId = $request->get('country_id');

        $query = DB::table('currency_rates')
            ->join('countries', 'currency_rates.country_id', '=', 'countries.id')
            ->select(
                'currency_rates.id',
                'countries.id as country_id',
                'countries.name as country_name',
                'countries.region',
                'currency_rates.base_currency',
                'currency_rates.target_currency',
                'currency_rates.exchange_rate',
                'currency_rates.change_percentage',
                'currency_rates.currency_risk_score',
                'currency_rates.rate_date'
            );

        if ($countryId) {
            $query->where('countries.id', $countryId);
        }

        $currencies = $query
            ->orderByDesc('currency_rates.currency_risk_score')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Data kurs berhasil diambil.',
            'data' => $currencies
        ]);
    }

    public function summary()
    {
        $summary = [
            'countries_count' => DB::table('countries')->count(),
            'ports_count' => DB::table('ports')->count(),
            'high_risk_count' => DB::table('risk_scores')
                ->where('total_score', '>=', 60)
                ->count(),
            'medium_risk_count' => DB::table('risk_scores')
                ->whereBetween('total_score', [35, 59])
                ->count(),
            'low_risk_count' => DB::table('risk_scores')
                ->where('total_score', '<', 35)
                ->count(),
            'news_count' => DB::table('news_cache')->count(),
            'negative_news_count' => DB::table('news_cache')
                ->where('sentiment', 'Negative')
                ->count(),
            'watchlist_count' => DB::table('watchlists')->count(),
        ];

        return response()->json([
            'status' => true,
            'message' => 'Ringkasan sistem berhasil diambil.',
            'data' => $summary
        ]);
    }
}