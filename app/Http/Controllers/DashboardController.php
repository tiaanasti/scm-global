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
    public function comparisons(Request $request)
{
    $countries = DB::table('countries')
        ->orderBy('name')
        ->get();

    $firstCountryId = $request->get('first_country_id') ?? ($countries->first()->id ?? null);
    $secondCountryId = $request->get('second_country_id') ?? ($countries->skip(1)->first()->id ?? $firstCountryId);

    $firstCountry = DB::table('countries')
        ->where('id', $firstCountryId)
        ->first();

    $secondCountry = DB::table('countries')
        ->where('id', $secondCountryId)
        ->first();

    $firstEconomic = DB::table('economic_indicators')
        ->where('country_id', $firstCountryId)
        ->latest('year')
        ->first();

    $secondEconomic = DB::table('economic_indicators')
        ->where('country_id', $secondCountryId)
        ->latest('year')
        ->first();

    $firstWeather = DB::table('weather_reports')
        ->where('country_id', $firstCountryId)
        ->latest('reported_at')
        ->first();

    $secondWeather = DB::table('weather_reports')
        ->where('country_id', $secondCountryId)
        ->latest('reported_at')
        ->first();

    $firstCurrency = DB::table('currency_rates')
        ->where('country_id', $firstCountryId)
        ->latest('rate_date')
        ->first();

    $secondCurrency = DB::table('currency_rates')
        ->where('country_id', $secondCountryId)
        ->latest('rate_date')
        ->first();

    $firstRisk = DB::table('risk_scores')
        ->where('country_id', $firstCountryId)
        ->latest('score_date')
        ->first();

    $secondRisk = DB::table('risk_scores')
        ->where('country_id', $secondCountryId)
        ->latest('score_date')
        ->first();

    $firstNews = DB::table('news_cache')
        ->where('country_id', $firstCountryId)
        ->latest('published_at')
        ->limit(3)
        ->get();

    $secondNews = DB::table('news_cache')
        ->where('country_id', $secondCountryId)
        ->latest('published_at')
        ->limit(3)
        ->get();

    $firstScore = $firstRisk->total_score ?? 0;
    $secondScore = $secondRisk->total_score ?? 0;

    if ($firstScore < $secondScore) {
        $saferCountry = $firstCountry->name ?? '-';
        $recommendation = ($firstCountry->name ?? '-') . ' lebih aman untuk perencanaan impor karena memiliki skor risiko lebih rendah.';
    } elseif ($secondScore < $firstScore) {
        $saferCountry = $secondCountry->name ?? '-';
        $recommendation = ($secondCountry->name ?? '-') . ' lebih aman untuk perencanaan impor karena memiliki skor risiko lebih rendah.';
    } else {
        $saferCountry = 'Seimbang';
        $recommendation = 'Kedua negara memiliki tingkat risiko yang relatif seimbang, sehingga keputusan impor perlu mempertimbangkan faktor kurs, cuaca, dan berita terbaru.';
    }

    return view('comparisons.index', compact(
        'countries',
        'firstCountryId',
        'secondCountryId',
        'firstCountry',
        'secondCountry',
        'firstEconomic',
        'secondEconomic',
        'firstWeather',
        'secondWeather',
        'firstCurrency',
        'secondCurrency',
        'firstRisk',
        'secondRisk',
        'firstNews',
        'secondNews',
        'saferCountry',
        'recommendation'
    ));
    }
public function watchlists(Request $request)
{
    $adminUser = DB::table('users')
        ->where('email', 'admin@supplyrisk.test')
        ->first();

    $userId = $adminUser->id ?? 1;

    $watchlistRows = DB::table('watchlists')
        ->join('countries', 'watchlists.country_id', '=', 'countries.id')
        ->leftJoin('risk_scores', 'countries.id', '=', 'risk_scores.country_id')
        ->leftJoin('weather_reports', 'countries.id', '=', 'weather_reports.country_id')
        ->leftJoin('currency_rates', 'countries.id', '=', 'currency_rates.country_id')
        ->select(
            'watchlists.id as watchlist_id',
            'countries.id as country_id',
            'countries.name as country_name',
            'countries.region',
            'countries.currency_code',
            'risk_scores.total_score',
            'risk_scores.risk_level',
            'risk_scores.recommendation',
            'weather_reports.temperature',
            'weather_reports.weather_status',
            'currency_rates.base_currency',
            'currency_rates.target_currency',
            'currency_rates.exchange_rate',
            'currency_rates.change_percentage',
            'watchlists.created_at'
        )
        ->where('watchlists.user_id', $userId)
        ->orderByDesc('risk_scores.total_score')
        ->get();

    $watchlistedCountryIds = $watchlistRows
        ->pluck('country_id')
        ->toArray();

    $availableCountries = DB::table('countries')
        ->whereNotIn('id', $watchlistedCountryIds)
        ->orderBy('name')
        ->get();

    $summary = [
        'total_watchlist' => $watchlistRows->count(),
        'high_risk' => $watchlistRows->where('total_score', '>=', 60)->count(),
        'medium_risk' => $watchlistRows->whereBetween('total_score', [35, 59])->count(),
        'low_risk' => $watchlistRows->where('total_score', '<', 35)->count(),
    ];

    return view('watchlists.index', compact(
        'watchlistRows',
        'availableCountries',
        'summary'
    ));
}
    public function storeWatchlist(Request $request)
{
    $request->validate([
        'country_id' => 'required|exists:countries,id',
    ]);

    $adminUser = DB::table('users')
        ->where('email', 'admin@supplyrisk.test')
        ->first();

    $userId = $adminUser->id ?? 1;

    $exists = DB::table('watchlists')
        ->where('user_id', $userId)
        ->where('country_id', $request->country_id)
        ->exists();

    if (!$exists) {
        DB::table('watchlists')->insert([
            'user_id' => $userId,
            'country_id' => $request->country_id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    return redirect()
        ->route('watchlists.index')
        ->with('success', 'Negara berhasil ditambahkan ke watchlist.');
}
    public function destroyWatchlist($id)
{
    DB::table('watchlists')
        ->where('id', $id)
        ->delete();

    return redirect()
        ->route('watchlists.index')
        ->with('success', 'Negara berhasil dihapus dari watchlist.');
}
    public function admin()
{
    $summary = [
        'users_count' => DB::table('users')->count(),
        'countries_count' => DB::table('countries')->count(),
        'ports_count' => DB::table('ports')->count(),
        'articles_count' => DB::table('articles')->count(),
        'news_count' => DB::table('news_cache')->count(),
        'watchlists_count' => DB::table('watchlists')->count(),
        'positive_words_count' => DB::table('positive_words')->count(),
        'negative_words_count' => DB::table('negative_words')->count(),
        'api_logs_count' => DB::table('api_logs')->count(),
    ];

    $users = DB::table('users')
        ->select('id', 'name', 'email', 'created_at')
        ->orderBy('id')
        ->get();

    $countries = DB::table('countries')
        ->leftJoin('risk_scores', 'countries.id', '=', 'risk_scores.country_id')
        ->select(
            'countries.id',
            'countries.name',
            'countries.region',
            'countries.currency_code',
            'risk_scores.total_score',
            'risk_scores.risk_level'
        )
        ->orderBy('countries.name')
        ->get();

    $ports = DB::table('ports')
        ->leftJoin('countries', 'ports.country_id', '=', 'countries.id')
        ->select(
            'ports.id',
            'ports.name',
            'ports.city',
            'ports.status',
            'ports.port_risk_score',
            'countries.name as country_name'
        )
        ->orderByDesc('ports.port_risk_score')
        ->get();

    $articles = DB::table('articles')
        ->leftJoin('users', 'articles.user_id', '=', 'users.id')
        ->select(
            'articles.id',
            'articles.title',
            'articles.category',
            'articles.status',
            'articles.created_at',
            'users.name as author_name'
        )
        ->orderByDesc('articles.created_at')
        ->get();

    $apiLogs = DB::table('api_logs')
        ->orderByDesc('requested_at')
        ->limit(10)
        ->get();

    $positiveWords = DB::table('positive_words')
        ->orderBy('word')
        ->get();

    $negativeWords = DB::table('negative_words')
        ->orderBy('word')
        ->get();

    return view('admin.index', compact(
        'summary',
        'users',
        'countries',
        'ports',
        'articles',
        'apiLogs',
        'positiveWords',
        'negativeWords'
    ));
    }
    public function reports()
{
    $summary = [
        'countries_count' => DB::table('countries')->count(),
        'ports_count' => DB::table('ports')->count(),
        'news_count' => DB::table('news_cache')->count(),
        'watchlists_count' => DB::table('watchlists')->count(),
        'high_risk_count' => DB::table('risk_scores')->where('total_score', '>=', 60)->count(),
        'medium_risk_count' => DB::table('risk_scores')->whereBetween('total_score', [35, 59])->count(),
        'low_risk_count' => DB::table('risk_scores')->where('total_score', '<', 35)->count(),
    ];

    $riskRows = DB::table('risk_scores')
        ->join('countries', 'risk_scores.country_id', '=', 'countries.id')
        ->select(
            'countries.name as country_name',
            'countries.region',
            'risk_scores.weather_score',
            'risk_scores.inflation_score',
            'risk_scores.currency_score',
            'risk_scores.news_score',
            'risk_scores.total_score',
            'risk_scores.risk_level',
            'risk_scores.recommendation'
        )
        ->orderByDesc('risk_scores.total_score')
        ->get();

    $currencyRows = DB::table('currency_rates')
        ->join('countries', 'currency_rates.country_id', '=', 'countries.id')
        ->select(
            'countries.name as country_name',
            'currency_rates.base_currency',
            'currency_rates.target_currency',
            'currency_rates.exchange_rate',
            'currency_rates.change_percentage',
            'currency_rates.currency_risk_score'
        )
        ->orderByDesc('currency_rates.currency_risk_score')
        ->get();

    $portRows = DB::table('ports')
        ->leftJoin('countries', 'ports.country_id', '=', 'countries.id')
        ->select(
            'ports.name',
            'ports.city',
            'ports.status',
            'ports.port_risk_score',
            'countries.name as country_name'
        )
        ->orderByDesc('ports.port_risk_score')
        ->get();

    $newsRows = DB::table('news_cache')
        ->leftJoin('countries', 'news_cache.country_id', '=', 'countries.id')
        ->select(
            'news_cache.title',
            'news_cache.category',
            'news_cache.sentiment',
            'news_cache.positive_score',
            'news_cache.negative_score',
            'countries.name as country_name'
        )
        ->latest('news_cache.published_at')
        ->get();

    $highestRisk = $riskRows->first();

    $lowestRisk = $riskRows
        ->sortBy('total_score')
        ->first();

    return view('reports.index', compact(
        'summary',
        'riskRows',
        'currencyRows',
        'portRows',
        'newsRows',
        'highestRisk',
        'lowestRisk'
    ));
    }
    public function storeCountry(Request $request)
{
    $request->validate([
        'country_code' => 'required|string|max:10|unique:countries,country_code',
        'name' => 'required|string|max:255',
        'capital' => 'nullable|string|max:255',
        'region' => 'nullable|string|max:255',
        'currency_code' => 'required|string|max:10',
        'currency_name' => 'nullable|string|max:255',
        'language' => 'nullable|string|max:255',
        'latitude' => 'nullable|numeric',
        'longitude' => 'nullable|numeric',
        'exchange_rate' => 'nullable|numeric',
    ]);

    DB::transaction(function () use ($request) {
        $now = now();

        $countryId = DB::table('countries')->insertGetId([
            'country_code' => strtoupper($request->country_code),
            'name' => $request->name,
            'capital' => $request->capital,
            'region' => $request->region,
            'currency_code' => strtoupper($request->currency_code),
            'currency_name' => $request->currency_name,
            'language' => $request->language,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('economic_indicators')->insert([
            'country_id' => $countryId,
            'year' => 2024,
            'gdp' => 1000000000000,
            'inflation_rate' => 3.0,
            'population' => 50000000,
            'exports' => 200000000000,
            'imports' => 180000000000,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('weather_reports')->insert([
            'country_id' => $countryId,
            'temperature' => 27,
            'rainfall' => 8,
            'wind_speed' => 12,
            'weather_status' => 'Berawan',
            'weather_risk_score' => 35,
            'reported_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('currency_rates')->insert([
            'country_id' => $countryId,
            'base_currency' => 'USD',
            'target_currency' => strtoupper($request->currency_code),
            'exchange_rate' => $request->exchange_rate ?? 1,
            'change_percentage' => 0.25,
            'currency_risk_score' => 35,
            'rate_date' => now()->toDateString(),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('risk_scores')->insert([
            'country_id' => $countryId,
            'weather_score' => 35,
            'inflation_score' => 30,
            'currency_score' => 35,
            'news_score' => 25,
            'total_score' => 35,
            'risk_level' => 'Risiko Sedang',
            'recommendation' => 'Negara ini baru ditambahkan ke sistem. Data awal masih bersifat default dan perlu diperbarui melalui API atau input admin.',
            'score_date' => now()->toDateString(),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('ports')->insert([
            'country_id' => $countryId,
            'name' => 'Main Port of ' . $request->name,
            'city' => $request->capital,
            'country_name' => $request->name,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'status' => 'Aman',
            'port_risk_score' => 30,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('news_cache')->insert([
            'country_id' => $countryId,
            'title' => 'Data awal negara ' . $request->name . ' berhasil ditambahkan',
            'description' => 'Negara ini baru ditambahkan ke sistem monitoring rantai pasok dan siap dipantau melalui dashboard.',
            'source' => 'System Demo',
            'url' => '#',
            'category' => 'Sistem',
            'sentiment' => 'Neutral',
            'positive_score' => 1,
            'negative_score' => 1,
            'published_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    });

    return redirect()
        ->route('admin.index')
        ->with('success', 'Negara baru berhasil ditambahkan ke sistem.');
    }
}