<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $countries = DB::table('countries')
            ->orderBy('name')
            ->get();

        $defaultCountry = DB::table('countries')
            ->where('country_code', 'ID')
            ->first();

        $selectedCountryId = $request->get('country_id') ?? ($defaultCountry->id ?? null);

        $country = DB::table('countries')
            ->where('id', $selectedCountryId)
            ->first();

        $economic = DB::table('economic_indicators')
            ->where('country_id', $selectedCountryId)
            ->latest('year')
            ->first();

        $weather = DB::table('weather_reports')
            ->where('country_id', $selectedCountryId)
            ->latest('reported_at')
            ->first();

        $currency = DB::table('currency_rates')
            ->where('country_id', $selectedCountryId)
            ->latest('rate_date')
            ->first();

        $risk = DB::table('risk_scores')
            ->where('country_id', $selectedCountryId)
            ->latest('score_date')
            ->first();

        $news = DB::table('news_cache')
            ->where('country_id', $selectedCountryId)
            ->latest('published_at')
            ->limit(5)
            ->get();

        $ports = DB::table('ports')
            ->where('country_id', $selectedCountryId)
            ->limit(5)
            ->get();

        $allPorts = DB::table('ports')
            ->join('countries', 'ports.country_id', '=', 'countries.id')
            ->select(
                'ports.id',
                'ports.name',
                'ports.city',
                'ports.latitude',
                'ports.longitude',
                'ports.status',
                'ports.port_risk_score',
                'countries.name as country_name'
            )
            ->get();

        $summary = [
            'countries_count' => DB::table('countries')->count(),

            'weather_alerts' => DB::table('weather_reports')
                ->where('weather_risk_score', '>=', 50)
                ->count(),

            'high_risk_count' => DB::table('risk_scores')
                ->where('total_score', '>=', 60)
                ->count(),

            'negative_news_count' => DB::table('news_cache')
                ->where('sentiment', 'Negative')
                ->count(),

            'ports_count' => DB::table('ports')->count(),
        ];

        return view('dashboard', compact(
            'countries',
            'selectedCountryId',
            'country',
            'economic',
            'weather',
            'currency',
            'risk',
            'news',
            'ports',
            'allPorts',
            'summary'
        ));
    }

    public function countries(Request $request)
    {
        $countries = DB::table('countries')
            ->orderBy('name')
            ->get();

        $selectedCountryId = $request->get('country_id') ?? ($countries->first()->id ?? null);

        $selectedCountry = DB::table('countries')
            ->where('id', $selectedCountryId)
            ->first();

        $economic = DB::table('economic_indicators')
            ->where('country_id', $selectedCountryId)
            ->latest('year')
            ->first();

        $weather = DB::table('weather_reports')
            ->where('country_id', $selectedCountryId)
            ->latest('reported_at')
            ->first();

        $currency = DB::table('currency_rates')
            ->where('country_id', $selectedCountryId)
            ->latest('rate_date')
            ->first();

        $risk = DB::table('risk_scores')
            ->where('country_id', $selectedCountryId)
            ->latest('score_date')
            ->first();

        $ports = DB::table('ports')
            ->where('country_id', $selectedCountryId)
            ->get();

        $news = DB::table('news_cache')
            ->where('country_id', $selectedCountryId)
            ->latest('published_at')
            ->limit(5)
            ->get();

        $countryRows = DB::table('countries')
            ->leftJoin('economic_indicators', 'countries.id', '=', 'economic_indicators.country_id')
            ->leftJoin('risk_scores', 'countries.id', '=', 'risk_scores.country_id')
            ->select(
                'countries.id',
                'countries.name',
                'countries.region',
                'countries.currency_code',
                'economic_indicators.gdp',
                'economic_indicators.inflation_rate',
                'economic_indicators.population',
                'risk_scores.total_score',
                'risk_scores.risk_level'
            )
            ->orderBy('countries.name')
            ->get();

        return view('countries.index', compact(
            'countries',
            'selectedCountryId',
            'selectedCountry',
            'economic',
            'weather',
            'currency',
            'risk',
            'ports',
            'news',
            'countryRows'
        ));
    }
    public function risks(Request $request)
{
    $countries = DB::table('countries')
        ->orderBy('name')
        ->get();

    $defaultCountry = DB::table('countries')
        ->where('country_code', 'ID')
        ->first();

    $selectedCountryId = $request->get('country_id') ?? ($defaultCountry->id ?? null);

    $country = DB::table('countries')
        ->where('id', $selectedCountryId)
        ->first();

    $risk = DB::table('risk_scores')
        ->where('country_id', $selectedCountryId)
        ->latest('score_date')
        ->first();

    $weather = DB::table('weather_reports')
        ->where('country_id', $selectedCountryId)
        ->latest('reported_at')
        ->first();

    $economic = DB::table('economic_indicators')
        ->where('country_id', $selectedCountryId)
        ->latest('year')
        ->first();

    $currency = DB::table('currency_rates')
        ->where('country_id', $selectedCountryId)
        ->latest('rate_date')
        ->first();

    $news = DB::table('news_cache')
        ->where('country_id', $selectedCountryId)
        ->latest('published_at')
        ->limit(5)
        ->get();

    $riskRows = DB::table('risk_scores')
        ->join('countries', 'risk_scores.country_id', '=', 'countries.id')
        ->select(
            'countries.name as country_name',
            'risk_scores.weather_score',
            'risk_scores.inflation_score',
            'risk_scores.currency_score',
            'risk_scores.news_score',
            'risk_scores.total_score',
            'risk_scores.risk_level',
            'risk_scores.recommendation',
            'risk_scores.score_date'
        )
        ->orderByDesc('risk_scores.total_score')
        ->get();

    return view('risks.index', compact(
        'countries',
        'selectedCountryId',
        'country',
        'risk',
        'weather',
        'economic',
        'currency',
        'news',
        'riskRows'
    ));
    }
    public function ports(Request $request)
{
    $countries = DB::table('countries')
        ->orderBy('name')
        ->get();

    $selectedCountryId = $request->get('country_id');

    $portsQuery = DB::table('ports')
        ->leftJoin('countries', 'ports.country_id', '=', 'countries.id')
        ->select(
            'ports.id',
            'ports.name',
            'ports.city',
            'ports.country_name',
            'ports.latitude',
            'ports.longitude',
            'ports.status',
            'ports.port_risk_score',
            'countries.name as country_real_name',
            'countries.currency_code',
            'countries.region'
        );

    if ($selectedCountryId) {
        $portsQuery->where('ports.country_id', $selectedCountryId);
    }

    $ports = $portsQuery
        ->orderBy('ports.name')
        ->get();

    $selectedCountry = null;

    if ($selectedCountryId) {
        $selectedCountry = DB::table('countries')
            ->where('id', $selectedCountryId)
            ->first();
    }

    $summary = [
        'total_ports' => $ports->count(),
        'safe_ports' => $ports->where('status', 'Aman')->count(),
        'warning_ports' => $ports->where('status', 'Waspada')->count(),
        'alert_ports' => $ports->where('status', 'Siaga')->count(),
    ];

    return view('ports.index', compact(
        'countries',
        'selectedCountryId',
        'selectedCountry',
        'ports',
        'summary'
    ));
    }
    public function currencies(Request $request)
{
    $countries = DB::table('countries')
        ->orderBy('name')
        ->get();

    $defaultCountry = DB::table('countries')
        ->where('country_code', 'ID')
        ->first();

    $selectedCountryId = $request->get('country_id') ?? ($defaultCountry->id ?? null);

    $country = DB::table('countries')
        ->where('id', $selectedCountryId)
        ->first();

    $currency = DB::table('currency_rates')
        ->where('country_id', $selectedCountryId)
        ->latest('rate_date')
        ->first();

    $risk = DB::table('risk_scores')
        ->where('country_id', $selectedCountryId)
        ->latest('score_date')
        ->first();

    $economic = DB::table('economic_indicators')
        ->where('country_id', $selectedCountryId)
        ->latest('year')
        ->first();

    $currencyRows = DB::table('currency_rates')
        ->join('countries', 'currency_rates.country_id', '=', 'countries.id')
        ->select(
            'countries.name as country_name',
            'countries.region',
            'countries.currency_code',
            'currency_rates.base_currency',
            'currency_rates.target_currency',
            'currency_rates.exchange_rate',
            'currency_rates.change_percentage',
            'currency_rates.currency_risk_score',
            'currency_rates.rate_date'
        )
        ->orderByDesc('currency_rates.currency_risk_score')
        ->get();

    $exchangeRate = $currency->exchange_rate ?? 0;

    $currencyTrend = [
        round($exchangeRate * 0.96, 2),
        round($exchangeRate * 0.98, 2),
        round($exchangeRate * 0.97, 2),
        round($exchangeRate * 1.01, 2),
        round($exchangeRate * 1.03, 2),
        round($exchangeRate, 2),
    ];

    return view('currencies.index', compact(
        'countries',
        'selectedCountryId',
        'country',
        'currency',
        'risk',
        'economic',
        'currencyRows',
        'currencyTrend'
    ));
    }
    public function news(Request $request)
{
    $countries = DB::table('countries')
        ->orderBy('name')
        ->get();

    $defaultCountry = DB::table('countries')
        ->where('country_code', 'ID')
        ->first();

    $selectedCountryId = $request->get('country_id') ?? ($defaultCountry->id ?? null);

    $country = DB::table('countries')
        ->where('id', $selectedCountryId)
        ->first();

    $newsItems = DB::table('news_cache')
        ->where('country_id', $selectedCountryId)
        ->latest('published_at')
        ->get();

    $allNews = DB::table('news_cache')
        ->leftJoin('countries', 'news_cache.country_id', '=', 'countries.id')
        ->select(
            'news_cache.id',
            'news_cache.title',
            'news_cache.description',
            'news_cache.source',
            'news_cache.category',
            'news_cache.sentiment',
            'news_cache.positive_score',
            'news_cache.negative_score',
            'news_cache.published_at',
            'countries.name as country_name'
        )
        ->latest('news_cache.published_at')
        ->get();

    $positiveCount = $newsItems->where('sentiment', 'Positive')->count();
    $neutralCount = $newsItems->where('sentiment', 'Neutral')->count();
    $negativeCount = $newsItems->where('sentiment', 'Negative')->count();
    $totalNews = max($newsItems->count(), 1);

    $sentimentSummary = [
        'positive_count' => $positiveCount,
        'neutral_count' => $neutralCount,
        'negative_count' => $negativeCount,
        'positive_percentage' => round(($positiveCount / $totalNews) * 100),
        'neutral_percentage' => round(($neutralCount / $totalNews) * 100),
        'negative_percentage' => round(($negativeCount / $totalNews) * 100),
    ];

    $negativeWords = DB::table('negative_words')
        ->orderBy('word')
        ->limit(8)
        ->get();

    $positiveWords = DB::table('positive_words')
        ->orderBy('word')
        ->limit(8)
        ->get();

    return view('news.index', compact(
        'countries',
        'selectedCountryId',
        'country',
        'newsItems',
        'allNews',
        'sentimentSummary',
        'negativeWords',
        'positiveWords'
    ));
}

}