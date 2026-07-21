<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

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

        $economic = $this->latestEconomicForCountry($selectedCountryId);

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

        $riskTrend = $this->riskTrendForCountry($selectedCountryId);

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
            'summary',
            'riskTrend'
        ));
    }

    public function countries(Request $request)
    {
        $countries = DB::table('countries')
            ->orderBy('name')
            ->get();

        $defaultCountry = DB::table('countries')
            ->where('country_code', 'ID')
            ->first();

        $selectedCountryId = $request->get('country_id') ?? ($defaultCountry->id ?? ($countries->first()->id ?? null));

        $selectedCountry = DB::table('countries')
            ->where('id', $selectedCountryId)
            ->first();

        $economic = $this->latestEconomicForCountry($selectedCountryId);

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

        $portsCount = 0;
        $ports = collect();

        if ($selectedCountryId) {
            $portsCount = DB::table('ports')
                ->where('country_id', $selectedCountryId)
                ->count();

            if ($portsCount === 0 && $selectedCountry?->name) {
                $portsCount = DB::table('ports')
                    ->where('country_name', $selectedCountry->name)
                    ->count();
            }

            $ports = DB::table('ports')
                ->where(function ($query) use ($selectedCountryId, $selectedCountry) {
                    $query->where('country_id', $selectedCountryId);

                    if ($selectedCountry?->name) {
                        $query->orWhere('country_name', $selectedCountry->name);
                    }
                })
                ->orderByDesc('created_at')
                ->limit(5)
                ->get()
                ->unique('id')
                ->values();
        }

        $news = DB::table('news_cache')
            ->where('country_id', $selectedCountryId)
            ->latest('published_at')
            ->limit(5)
            ->get();

        $isLandlocked = $this->isLandlockedCountry(
            optional($selectedCountry)->country_code
        );

        /*
         * Ambil hanya tahun ekonomi terbaru yang benar-benar memiliki data.
         * Baris placeholder tahun berjalan yang semuanya 0 tidak akan dipilih.
         */
        $latestEconomicYears = DB::table('economic_indicators')
            ->select(
                'country_id',
                DB::raw('MAX(year) as latest_year')
            )
            ->where(function ($query) {
                $query->where('gdp', '>', 0)
                    ->orWhere('exports', '>', 0)
                    ->orWhere('imports', '>', 0)
                    ->orWhere('inflation_rate', '<>', 0);
            })
            ->groupBy('country_id');

        /*
         * Ambil satu data risiko terbaru untuk setiap negara agar tabel
         * tidak menghasilkan baris negara yang berulang.
         */
        $latestRiskIds = DB::table('risk_scores')
            ->select(
                'country_id',
                DB::raw('MAX(id) as latest_id')
            )
            ->groupBy('country_id');

        $countryRows = DB::table('countries')
            ->leftJoinSub(
                $latestEconomicYears,
                'latest_economic_years',
                function ($join) {
                    $join->on(
                        'countries.id',
                        '=',
                        'latest_economic_years.country_id'
                    );
                }
            )
            ->leftJoin('economic_indicators', function ($join) {
                $join->on(
                    'countries.id',
                    '=',
                    'economic_indicators.country_id'
                )->on(
                    'economic_indicators.year',
                    '=',
                    'latest_economic_years.latest_year'
                );
            })
            ->leftJoinSub(
                $latestRiskIds,
                'latest_risk_ids',
                function ($join) {
                    $join->on(
                        'countries.id',
                        '=',
                        'latest_risk_ids.country_id'
                    );
                }
            )
            ->leftJoin(
                'risk_scores',
                'risk_scores.id',
                '=',
                'latest_risk_ids.latest_id'
            )
            ->select(
                'countries.id',
                'countries.name',
                'countries.region',
                'countries.currency_code',
                'economic_indicators.year',
                'economic_indicators.gdp',
                'economic_indicators.inflation_rate',
                'economic_indicators.population',
                'risk_scores.total_score',
                'risk_scores.risk_level'
            )
            ->orderBy('countries.name')
            ->get();

        $gdpTrend = $this->gdpTrendForCountry($selectedCountryId);
        $inflationTrend = $this->inflationTrendForCountry($selectedCountryId);

        return view('countries.index', compact(
            'countries',
            'selectedCountryId',
            'selectedCountry',
            'economic',
            'weather',
            'currency',
            'risk',
            'ports',
            'portsCount',
            'isLandlocked',
            'news',
            'countryRows',
            'gdpTrend',
            'inflationTrend'
        ));
    }

    private function isLandlockedCountry(?string $countryCode): bool
    {
        if (!$countryCode) {
            return false;
        }

        $landlocked = [
            'AF', 'AM', 'AT', 'AZ', 'BI', 'BO', 'BW', 'BF', 'CF', 'CH',
            'CZ', 'ET', 'HU', 'KZ', 'KG', 'LA', 'LS', 'LI', 'LU', 'ML',
            'MD', 'MN', 'NP', 'MK', 'NE', 'PY', 'RW', 'RS', 'SK', 'SS',
            'TJ', 'TM', 'UG', 'UZ', 'ZM', 'ZW'
        ];

        return in_array(strtoupper(trim($countryCode)), $landlocked, true);
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

    $economic = $this->latestEconomicForCountry($selectedCountryId);

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

    $riskTrend = $this->riskTrendForCountry($selectedCountryId);

    return view('risks.index', compact(
        'countries',
        'selectedCountryId',
        'country',
        'risk',
        'weather',
        'economic',
        'currency',
        'news',
        'riskRows',
        'riskTrend'
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
        'safe_ports' => $ports->whereIn('status', ['Aman', 'Normal'])->count(),
        'warning_ports' => $ports->where('status', 'Waspada')->count(),
        'alert_ports' => $ports->whereIn('status', ['Siaga', 'Darurat'])->count(),
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

    $economic = $this->latestEconomicForCountry($selectedCountryId);

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

    $currencyTrend = $this->currencyTrendForCountry($selectedCountryId);

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

    $firstEconomic = $this->latestEconomicForCountry($firstCountryId);

    $secondEconomic = $this->latestEconomicForCountry($secondCountryId);

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
    $userId = Auth::id();

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

    $userId = Auth::id();

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
    $deleted = DB::table('watchlists')
        ->where('id', $id)
        ->where('user_id', Auth::id())
        ->delete();

    if (!$deleted) {
        return redirect()
            ->route('watchlists.index')
            ->with('error', 'Watchlist tidak ditemukan atau Anda tidak memiliki akses.');
    }

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
        ->select('id', 'name', 'email', 'role', 'created_at')
        ->orderBy('name')
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

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,user',
        ]);

        $now = now();

        DB::table('users')->insert([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return redirect()
            ->route('admin.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    public function editUser($id)
    {
        $user = DB::table('users')->where('id', $id)->first();

        if (!$user) {
            return redirect()
                ->route('admin.index')
                ->with('error', 'User tidak ditemukan.');
        }

        return view('admin.users.edit', compact('user'));
    }

    public function updateUser(Request $request, $id)
    {
        $user = DB::table('users')->where('id', $id)->first();

        if (!$user) {
            return redirect()
                ->route('admin.index')
                ->with('error', 'User tidak ditemukan.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'role' => 'required|in:admin,user',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if ($user->role === 'admin' && $validated['role'] === 'user' && $this->countAdminUsers() <= 1) {
            return back()
                ->with('error', 'Minimal satu akun admin harus tetap tersedia.')
                ->withInput();
        }

        $payload = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'updated_at' => now(),
        ];

        if (!empty($validated['password'])) {
            $payload['password'] = Hash::make($validated['password']);
        }

        DB::table('users')->where('id', $id)->update($payload);

        return redirect()
            ->route('admin.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    public function destroyUser($id)
    {
        if ((int) $id === (int) Auth::id()) {
            return redirect()
                ->route('admin.index')
                ->with('error', 'Anda tidak boleh menghapus akun sendiri.');
        }

        $user = DB::table('users')->where('id', $id)->first();

        if (!$user) {
            return redirect()
                ->route('admin.index')
                ->with('error', 'User tidak ditemukan.');
        }

        if ($user->role === 'admin' && $this->countAdminUsers() <= 1) {
            return redirect()
                ->route('admin.index')
                ->with('error', 'Minimal satu akun admin harus tetap tersedia.');
        }

        DB::table('users')->where('id', $id)->delete();

        return redirect()
            ->route('admin.index')
            ->with('success', 'User berhasil dihapus.');
    }

    private function countAdminUsers(): int
    {
        return DB::table('users')->where('role', 'admin')->count();
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
    public function destroyCountry($id)
{
    $country = DB::table('countries')
        ->where('id', $id)
        ->first();

    if (!$country) {
        return redirect()
            ->route('admin.index')
            ->with('error', 'Negara tidak ditemukan.');
    }

    DB::transaction(function () use ($id) {
        DB::table('watchlists')->where('country_id', $id)->delete();
        DB::table('risk_scores')->where('country_id', $id)->delete();
        DB::table('currency_rates')->where('country_id', $id)->delete();
        DB::table('weather_reports')->where('country_id', $id)->delete();
        DB::table('economic_indicators')->where('country_id', $id)->delete();
        DB::table('ports')->where('country_id', $id)->delete();
        DB::table('news_cache')->where('country_id', $id)->delete();

        DB::table('countries')->where('id', $id)->delete();
    });

    return redirect()
        ->route('admin.index')
        ->with('success', 'Negara berhasil dihapus dari sistem.');
    }
    public function editCountry($id)
{
    $country = DB::table('countries')
        ->where('id', $id)
        ->first();

    if (!$country) {
        return redirect()
            ->route('admin.index')
            ->with('error', 'Negara tidak ditemukan.');
    }

    $economic = $this->latestEconomicForCountry($id);

    $weather = DB::table('weather_reports')
        ->where('country_id', $id)
        ->latest('reported_at')
        ->first();

    $currency = DB::table('currency_rates')
        ->where('country_id', $id)
        ->latest('rate_date')
        ->first();

    $risk = DB::table('risk_scores')
        ->where('country_id', $id)
        ->latest('score_date')
        ->first();

    return view('admin.countries.edit', compact(
        'country',
        'economic',
        'weather',
        'currency',
        'risk'
    ));
    }
    public function updateCountry(Request $request, $id)
{
    $country = DB::table('countries')
        ->where('id', $id)
        ->first();

    if (!$country) {
        return redirect()
            ->route('admin.index')
            ->with('error', 'Negara tidak ditemukan.');
    }

    $request->validate([
        'country_code' => 'required|string|max:10|unique:countries,country_code,' . $id,
        'name' => 'required|string|max:255',
        'capital' => 'nullable|string|max:255',
        'region' => 'nullable|string|max:255',
        'currency_code' => 'required|string|max:10',
        'currency_name' => 'nullable|string|max:255',
        'language' => 'nullable|string|max:255',
        'latitude' => 'nullable|numeric',
        'longitude' => 'nullable|numeric',

        'gdp' => 'nullable|numeric',
        'inflation_rate' => 'nullable|numeric',
        'population' => 'nullable|numeric',
        'exports' => 'nullable|numeric',
        'imports' => 'nullable|numeric',

        'temperature' => 'nullable|numeric',
        'weather_status' => 'nullable|string|max:255',

        'exchange_rate' => 'nullable|numeric',
        'change_percentage' => 'nullable|numeric',

        'weather_score' => 'nullable|numeric|min:0|max:100',
        'inflation_score' => 'nullable|numeric|min:0|max:100',
        'currency_score' => 'nullable|numeric|min:0|max:100',
        'news_score' => 'nullable|numeric|min:0|max:100',
        'total_score' => 'nullable|numeric|min:0|max:100',
        'risk_level' => 'nullable|string|max:255',
        'recommendation' => 'nullable|string',
    ]);

    DB::transaction(function () use ($request, $id) {
        $now = now();

        DB::table('countries')
            ->where('id', $id)
            ->update([
                'country_code' => strtoupper($request->country_code),
                'name' => $request->name,
                'capital' => $request->capital,
                'region' => $request->region,
                'currency_code' => strtoupper($request->currency_code),
                'currency_name' => $request->currency_name,
                'language' => $request->language,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'updated_at' => $now,
            ]);

        DB::table('economic_indicators')
            ->updateOrInsert(
                ['country_id' => $id, 'year' => 2024],
                [
                    'gdp' => $request->gdp ?? 0,
                    'inflation_rate' => $request->inflation_rate ?? 0,
                    'population' => $request->population ?? 0,
                    'exports' => $request->exports ?? 0,
                    'imports' => $request->imports ?? 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );

        DB::table('weather_reports')
            ->updateOrInsert(
                ['country_id' => $id],
                [
                    'temperature' => $request->temperature ?? 0,
                    'rainfall' => 8,
                    'wind_speed' => 12,
                    'weather_status' => $request->weather_status ?? 'Berawan',
                    'weather_risk_score' => $request->weather_score ?? 0,
                    'reported_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );

        $this->storeCurrencyRateSnapshot($id, [
            'base_currency' => 'USD',
            'target_currency' => strtoupper($request->currency_code),
            'exchange_rate' => $request->exchange_rate ?? 1,
            'change_percentage' => $request->change_percentage ?? 0,
            'currency_risk_score' => $request->currency_score ?? 0,
            'rate_date' => now()->toDateString(),
        ], $now);

        $this->storeRiskScoreSnapshot($id, [
            'weather_score' => $request->weather_score ?? 0,
            'inflation_score' => $request->inflation_score ?? 0,
            'currency_score' => $request->currency_score ?? 0,
            'news_score' => $request->news_score ?? 0,
            'total_score' => $request->total_score ?? 0,
            'risk_level' => $request->risk_level ?? 'Risiko Rendah',
            'recommendation' => $request->recommendation,
            'score_date' => now()->toDateString(),
        ], $now);

        DB::table('ports')
            ->where('country_id', $id)
            ->update([
                'country_name' => $request->name,
                'updated_at' => $now,
            ]);
    });

    return redirect()
        ->route('admin.index')
        ->with('success', 'Data negara berhasil diperbarui.');
    }
    public function storeArticle(Request $request)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'category' => 'nullable|string|max:255',
        'status' => 'required|string|in:Draft,Published',
        'content' => 'required|string',
    ]);

    DB::table('articles')->insert([
        'user_id' => Auth::id(),
        'title' => $request->title,
        'category' => $request->category,
        'status' => $request->status,
        'content' => $request->content,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return redirect()
        ->route('admin.index')
        ->with('success', 'Artikel analisis berhasil ditambahkan.');
    }
    public function editArticle($id)
{
    $article = DB::table('articles')
        ->where('id', $id)
        ->first();

    if (!$article) {
        return redirect()
            ->route('admin.index')
            ->with('error', 'Artikel tidak ditemukan.');
    }

    return view('admin.articles.edit', compact('article'));
    }
    public function updateArticle(Request $request, $id)
{
    $article = DB::table('articles')
        ->where('id', $id)
        ->first();

    if (!$article) {
        return redirect()
            ->route('admin.index')
            ->with('error', 'Artikel tidak ditemukan.');
    }

    $request->validate([
        'title' => 'required|string|max:255',
        'category' => 'nullable|string|max:255',
        'status' => 'required|string|in:Draft,Published',
        'content' => 'required|string',
    ]);

    DB::table('articles')
        ->where('id', $id)
        ->update([
            'title' => $request->title,
            'category' => $request->category,
            'status' => $request->status,
            'content' => $request->content,
            'updated_at' => now(),
        ]);

    return redirect()
        ->route('admin.index')
        ->with('success', 'Artikel analisis berhasil diperbarui.');
    }
    public function destroyArticle($id)
{
    $article = DB::table('articles')
        ->where('id', $id)
        ->first();

    if (!$article) {
        return redirect()
            ->route('admin.index')
            ->with('error', 'Artikel tidak ditemukan.');
    }

    DB::table('articles')
        ->where('id', $id)
        ->delete();

    return redirect()
        ->route('admin.index')
        ->with('success', 'Artikel analisis berhasil dihapus.');
    }
    public function recalculateRiskScores()
{
    $countries = DB::table('countries')->get();

    foreach ($countries as $country) {
        $economic = $this->latestEconomicForCountry($country->id);

        $weather = DB::table('weather_reports')
            ->where('country_id', $country->id)
            ->latest('reported_at')
            ->first();

        $currency = DB::table('currency_rates')
            ->where('country_id', $country->id)
            ->latest('rate_date')
            ->first();

        $newsItems = DB::table('news_cache')
            ->where('country_id', $country->id)
            ->get();

        // SKOR CUACA
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

        // SKOR INFLASI
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

        // SKOR KURS
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

        // SKOR BERITA
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

        // TOTAL SCORE DENGAN BOBOT
        $totalScore = round(
            ($weatherScore * 0.25) +
            ($inflationScore * 0.30) +
            ($currencyScore * 0.25) +
            ($newsScore * 0.20)
        );

        // LEVEL RISIKO
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
    }

    return redirect()
        ->route('admin.index')
        ->with('success', 'Skor risiko semua negara berhasil dihitung ulang secara otomatis.');
    }
    public function syncExternalApis()
{
    $userId = Auth::id();

    /*
     |--------------------------------------------------------------------------
     | Hanya sinkronkan negara yang ada di Watchlist
     |--------------------------------------------------------------------------
     */
    $countries = DB::table('countries')
        ->join('watchlists', 'countries.id', '=', 'watchlists.country_id')
        ->where('watchlists.user_id', $userId)
        ->select('countries.*')
        ->distinct()
        ->orderBy('countries.name')
        ->get();

    if ($countries->isEmpty()) {
        return redirect()
            ->route('admin.index')
            ->with(
                'error',
                'Belum ada negara di Watchlist. Tambahkan minimal satu negara sebelum melakukan sinkronisasi API.'
            );
    }

    $now = now();
    $openMeteoUrl = config('services.open_meteo.forecast_url');
    $exchangeRateUrl = config('services.exchange_rate.latest_url');
    $timeout = (int) config('services.external_api.timeout', 15);

    $gnewsApiKey = config('services.gnews.api_key');
    $gnewsSearchUrl = config('services.gnews.search_url');
    $gnewsMaxArticles = (int) config('services.gnews.max_articles', 5);
    $gnewsLang = config('services.gnews.lang', 'en');

    $positiveWords = DB::table('positive_words')
        ->pluck('word')
        ->map(function ($word) {
            return strtolower($word);
        })
        ->toArray();

    $negativeWords = DB::table('negative_words')
        ->pluck('word')
        ->map(function ($word) {
            return strtolower($word);
        })
        ->toArray();

    $successCount = 0;
    $failedCount = 0;

    foreach ($countries as $country) {
        /*
        |--------------------------------------------------------------------------
        | 1. SYNC OPEN-METEO WEATHER API
        |--------------------------------------------------------------------------
        */
        if (!$country->latitude || !$country->longitude) {
            DB::table('api_logs')->insert([
                'api_name' => 'Open-Meteo',
                'endpoint' => $openMeteoUrl,
                'status' => 'Failed',
                'message' => 'Koordinat latitude/longitude untuk ' . $country->name . ' belum tersedia.',
                'requested_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $failedCount++;
        } else {
            try {
                $weatherResponse = Http::timeout($timeout)->get($openMeteoUrl, [
                    'latitude' => $country->latitude,
                    'longitude' => $country->longitude,
                    'current' => 'temperature_2m,precipitation,wind_speed_10m',
                    'timezone' => 'auto',
                ]);

                if ($weatherResponse->successful()) {
                    $weatherData = $weatherResponse->json();
                    $current = $weatherData['current'] ?? [];

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

                    DB::table('weather_reports')->updateOrInsert(
                        ['country_id' => $country->id],
                        [
                            'temperature' => $temperature,
                            'rainfall' => $rainfall,
                            'wind_speed' => $windSpeed,
                            'weather_status' => $weatherStatus,
                            'weather_risk_score' => $weatherRiskScore,
                            'reported_at' => $now,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]
                    );

                    DB::table('api_logs')->insert([
                        'api_name' => 'Open-Meteo',
                        'endpoint' => $openMeteoUrl,
                        'status' => 'Success',
                        'message' => 'Data cuaca ' . $country->name . ' berhasil diperbarui.',
                        'requested_at' => $now,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);

                    $successCount++;
                } else {
                    DB::table('api_logs')->insert([
                        'api_name' => 'Open-Meteo',
                        'endpoint' => $openMeteoUrl,
                        'status' => 'Failed',
                        'message' => 'Gagal mengambil data cuaca ' . $country->name . '. HTTP status: ' . $weatherResponse->status(),
                        'requested_at' => $now,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);

                    $failedCount++;
                }
            } catch (\Exception $exception) {
                DB::table('api_logs')->insert([
                    'api_name' => 'Open-Meteo',
                    'endpoint' => $openMeteoUrl,
                    'status' => 'Failed',
                    'message' => $country->name . ': ' . $exception->getMessage(),
                    'requested_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                $failedCount++;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 2. SYNC EXCHANGE RATE API
        |--------------------------------------------------------------------------
        */
        if (!$country->currency_code) {
            DB::table('api_logs')->insert([
                'api_name' => 'ExchangeRate-API',
                'endpoint' => $exchangeRateUrl,
                'status' => 'Failed',
                'message' => 'Kode mata uang untuk ' . $country->name . ' belum tersedia.',
                'requested_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $failedCount++;
        } else {
            try {
                $currencyResponse = Http::timeout($timeout)->get($exchangeRateUrl);

                if ($currencyResponse->successful()) {
                    $currencyData = $currencyResponse->json();
                    $rates = $currencyData['rates'] ?? [];

                    $targetCurrency = strtoupper($country->currency_code);
                    $exchangeRate = $rates[$targetCurrency] ?? null;

                    if ($exchangeRate) {
                        $oldCurrency = DB::table('currency_rates')
                            ->where('country_id', $country->id)
                            ->latest('rate_date')
                            ->first();

                        $oldRate = $oldCurrency->exchange_rate ?? $exchangeRate;

                        if ($oldRate > 0) {
                            $changePercentage = (($exchangeRate - $oldRate) / $oldRate) * 100;
                        } else {
                            $changePercentage = 0;
                        }

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
                            'rate_date' => now()->toDateString(),
                        ], $now);

                        DB::table('api_logs')->insert([
                            'api_name' => 'ExchangeRate-API',
                            'endpoint' => $exchangeRateUrl,
                            'status' => 'Success',
                            'message' => 'Data kurs ' . $targetCurrency . ' berhasil diperbarui.',
                            'requested_at' => $now,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]);

                        $successCount++;
                    } else {
                        DB::table('api_logs')->insert([
                            'api_name' => 'ExchangeRate-API',
                            'endpoint' => $exchangeRateUrl,
                            'status' => 'Failed',
                            'message' => 'Kode mata uang ' . $targetCurrency . ' tidak ditemukan pada response API.',
                            'requested_at' => $now,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]);

                        $failedCount++;
                    }
                } else {
                    DB::table('api_logs')->insert([
                        'api_name' => 'ExchangeRate-API',
                        'endpoint' => $exchangeRateUrl,
                        'status' => 'Failed',
                        'message' => 'Gagal mengambil data kurs untuk ' . $country->name . '. HTTP status: ' . $currencyResponse->status(),
                        'requested_at' => $now,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);

                    $failedCount++;
                }
            } catch (\Exception $exception) {
                DB::table('api_logs')->insert([
                    'api_name' => 'ExchangeRate-API',
                    'endpoint' => $exchangeRateUrl,
                    'status' => 'Failed',
                    'message' => $country->name . ': ' . $exception->getMessage(),
                    'requested_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                $failedCount++;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 3. SYNC GNEWS API
        |--------------------------------------------------------------------------
        */
        if (!$gnewsApiKey) {
            DB::table('api_logs')->insert([
                'api_name' => 'GNews API',
                'endpoint' => $gnewsSearchUrl ?? 'GNews Search Endpoint',
                'status' => 'Failed',
                'message' => 'GNEWS_API_KEY belum diisi di file .env.',
                'requested_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $failedCount++;
        } else {
            try {
                $query = $country->name . ' supply chain OR logistics OR port OR import OR export OR inflation OR currency';

                $newsResponse = Http::timeout($timeout)->get($gnewsSearchUrl, [
                    'q' => $query,
                    'lang' => $gnewsLang,
                    'max' => $gnewsMaxArticles,
                    'sortby' => 'publishedAt',
                    'apikey' => $gnewsApiKey,
                ]);

                if ($newsResponse->successful()) {
                    $newsData = $newsResponse->json();
                    $articles = $newsData['articles'] ?? [];

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
                        } catch (\Exception $exception) {
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

                        if ($negativeScore > $positiveScore) {
                            $sentiment = 'Negative';
                        } elseif ($positiveScore > $negativeScore) {
                            $sentiment = 'Positive';
                        } else {
                            $sentiment = 'Neutral';
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

                    DB::table('api_logs')->insert([
                        'api_name' => 'GNews API',
                        'endpoint' => $gnewsSearchUrl,
                        'status' => 'Success',
                        'message' => 'Berita ' . $country->name . ' berhasil diperbarui. Total artikel: ' . count($articles) . '.',
                        'requested_at' => $now,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);

                    $successCount++;
                } else {
                    DB::table('api_logs')->insert([
                        'api_name' => 'GNews API',
                        'endpoint' => $gnewsSearchUrl,
                        'status' => 'Failed',
                        'message' => 'Gagal mengambil berita untuk ' . $country->name . '. HTTP status: ' . $newsResponse->status(),
                        'requested_at' => $now,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);

                    $failedCount++;
                }
            } catch (\Exception $exception) {
                DB::table('api_logs')->insert([
                    'api_name' => 'GNews API',
                    'endpoint' => $gnewsSearchUrl ?? 'GNews Search Endpoint',
                    'status' => 'Failed',
                    'message' => $country->name . ': ' . $exception->getMessage(),
                    'requested_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                $failedCount++;
            }
        }
    }

    $this->recalculateRiskScoresAfterApiSync();

    return redirect()
    ->route('admin.index')
    ->with(
        'success',
        'Sinkronisasi API untuk '
        . $countries->count()
        . ' negara Watchlist selesai. Berhasil: '
        . $successCount
        . ', gagal: '
        . $failedCount
        . '.'
    );
    }
    private function recalculateRiskScoresAfterApiSync()
{
    $countries = DB::table('countries')->get();

    foreach ($countries as $country) {
        $economic = $this->latestEconomicForCountry($country->id);

        $weather = DB::table('weather_reports')
            ->where('country_id', $country->id)
            ->latest('reported_at')
            ->first();

        $currency = DB::table('currency_rates')
            ->where('country_id', $country->id)
            ->latest('rate_date')
            ->first();

        $newsItems = DB::table('news_cache')
            ->where('country_id', $country->id)
            ->get();

        $weatherScore = min($weather->weather_risk_score ?? 0, 100);

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

        $totalScore = round(
            ($weatherScore * 0.25) +
            ($inflationScore * 0.30) +
            ($currencyScore * 0.25) +
            ($newsScore * 0.20)
        );

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
    }
    }

    /**
     * Mengambil data ekonomi terbaru yang benar-benar memiliki isi.
     *
     * REST Countries sebelumnya membuat baris tahun berjalan dengan nilai
     * ekonomi 0. Karena tahunnya lebih baru, query latest('year') selalu
     * memilih baris kosong tersebut dan menutupi data World Bank.
     */
    private function latestEconomicForCountry($countryId)
    {
        if (!$countryId) {
            return null;
        }

        /*
         * Prioritaskan baris World Bank yang memiliki indikator ekonomi
         * lengkap. Baris REST Countries biasanya hanya berisi populasi,
         * sehingga tidak boleh mengalahkan baris World Bank.
         */
        return DB::table('economic_indicators')
            ->where('country_id', $countryId)
            ->orderByRaw(
                '(CASE WHEN COALESCE(gdp, 0) > 0 THEN 1 ELSE 0 END +
                  CASE WHEN COALESCE(exports, 0) > 0 THEN 1 ELSE 0 END +
                  CASE WHEN COALESCE(imports, 0) > 0 THEN 1 ELSE 0 END +
                  CASE WHEN COALESCE(inflation_rate, 0) <> 0 THEN 1 ELSE 0 END +
                  CASE WHEN COALESCE(population, 0) > 0 THEN 1 ELSE 0 END) DESC'
            )
            ->orderByRaw(
                'CASE WHEN COALESCE(gdp, 0) > 0 THEN 1 ELSE 0 END DESC'
            )
            ->orderByDesc('year')
            ->orderByDesc('id')
            ->first();
    }

    private function gdpTrendForCountry($countryId)
    {
        if (!$countryId) {
            return collect();
        }

        return DB::table('economic_indicators')
            ->where('country_id', $countryId)
            ->where('gdp', '>', 0)
            ->orderBy('year')
            ->get(['year', 'gdp']);
    }

    private function inflationTrendForCountry($countryId)
    {
        if (!$countryId) {
            return collect();
        }

        return DB::table('economic_indicators')
            ->where('country_id', $countryId)
            ->orderBy('year')
            ->get(['year', 'inflation_rate']);
    }

    private function currencyTrendForCountry($countryId, int $limit = 10)
    {
        if (!$countryId) {
            return collect();
        }

        return DB::table('currency_rates')
            ->where('country_id', $countryId)
            ->orderByDesc('rate_date')
            ->orderByDesc('id')
            ->limit($limit)
            ->get(['rate_date', 'exchange_rate'])
            ->sortBy('rate_date')
            ->values();
    }

    private function riskTrendForCountry($countryId, int $limit = 10)
    {
        if (!$countryId) {
            return collect();
        }

        return DB::table('risk_scores')
            ->where('country_id', $countryId)
            ->orderByDesc('score_date')
            ->orderByDesc('id')
            ->limit($limit)
            ->get(['score_date', 'total_score'])
            ->sortBy('score_date')
            ->values();
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
            DB::table('currency_rates')
                ->where('id', $existing->id)
                ->update($payload);

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
            DB::table('risk_scores')
                ->where('id', $existing->id)
                ->update($payload);

            return;
        }

        DB::table('risk_scores')->insert(array_merge($payload, [
            'country_id' => $countryId,
            'created_at' => $timestamp,
        ]));
    }

    public function syncCountriesFromApi()
    {
        $apiKey = config('services.rest_countries.api_key');
        $baseUrl = config(
            'services.rest_countries.base_url',
            'https://api.restcountries.com/countries/v5'
        );

        $limit = max(1, min((int) config('services.rest_countries.limit', 100), 100));
        $timeout = (int) config('services.external_api.timeout', 30);
        $now = now();

        if (!$apiKey) {
            return redirect()
                ->route('admin.index')
                ->with('error', 'REST_COUNTRIES_API_KEY belum diisi di file .env.');
        }

        $importedCount = 0;
        $updatedCount = 0;
        $skippedCount = 0;
        $offset = 0;
        $page = 0;
        $hasMore = true;

        /*
         * Mengubah nilai API menjadi teks aman untuk MySQL.
         * Ini mencegah error "Array to string conversion".
         */
        $flattenStrings = function ($value) use (&$flattenStrings): array {
            if ($value === null) {
                return [];
            }

            if (is_string($value) || is_numeric($value)) {
                $text = trim((string) $value);
                return $text !== '' ? [$text] : [];
            }

            if (!is_array($value)) {
                return [];
            }

            $result = [];

            foreach ($value as $item) {
                $result = array_merge($result, $flattenStrings($item));
            }

            return $result;
        };

        $normalizeText = function ($value) use ($flattenStrings): ?string {
            $values = array_values(array_unique($flattenStrings($value)));

            if (empty($values)) {
                return null;
            }

            return implode(', ', $values);
        };

        $normalizeNumber = function ($value): ?float {
            if (is_numeric($value)) {
                return (float) $value;
            }

            if (is_array($value)) {
                foreach ($value as $item) {
                    if (is_numeric($item)) {
                        return (float) $item;
                    }
                }
            }

            return null;
        };

        try {
            while ($hasMore && $page < 10) {
                $response = Http::timeout($timeout)
                    ->acceptJson()
                    ->withToken($apiKey)
                    ->get($baseUrl, [
                        'limit' => $limit,
                        'offset' => $offset,
                        'response_fields' => implode(',', [
                            'names.common',
                            'codes.alpha_2',
                            'capitals',
                            'region',
                            'currencies',
                            'languages',
                            'coordinates.lat',
                            'coordinates.lng',
                            'population',
                        ]),
                    ]);

                if (!$response->successful()) {
                    $errorBody = substr($response->body(), 0, 500);

                    DB::table('api_logs')->insert([
                        'api_name' => 'REST Countries API',
                        'endpoint' => $baseUrl,
                        'status' => 'Failed',
                        'message' => 'HTTP status: ' . $response->status() . '. Response: ' . $errorBody,
                        'requested_at' => $now,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);

                    return redirect()
                        ->route('admin.index')
                        ->with('error', 'Sinkronisasi negara gagal. HTTP status: ' . $response->status());
                }

                $responseData = $response->json();
                $countriesData = data_get($responseData, 'data.objects', []);
                $meta = data_get($responseData, 'data.meta', []);

                if (!is_array($countriesData)) {
                    $countriesData = [];
                }

                foreach ($countriesData as $item) {
                    $countryCode = $normalizeText(data_get($item, 'codes.alpha_2'));
                    $countryName = $normalizeText(data_get($item, 'names.common'));

                    if (!$countryCode || !$countryName) {
                        $skippedCount++;
                        continue;
                    }

                    $countryCode = strtoupper(substr($countryCode, 0, 10));
                    $countryName = substr($countryName, 0, 255);

                    /*
                     * capitals dapat berupa array objek, misalnya:
                     * [['name' => 'Kabul']]. Ambil hanya nama ibu kotanya.
                     */
                    $capitalNames = [];
                    $capitals = data_get($item, 'capitals', []);

                    if (!is_array($capitals)) {
                        $capitals = [$capitals];
                    }

                    foreach ($capitals as $capitalItem) {
                        if (is_array($capitalItem)) {
                            $capitalValue = $capitalItem['name']
                                ?? $capitalItem['common']
                                ?? $capitalItem['capital']
                                ?? null;

                            if ($capitalValue === null && count($capitalItem) === 1) {
                                $capitalValue = reset($capitalItem);
                            }
                        } else {
                            $capitalValue = $capitalItem;
                        }

                        $capitalText = $normalizeText($capitalValue);

                        if ($capitalText) {
                            $capitalNames[] = $capitalText;
                        }
                    }

                    $capital = !empty($capitalNames)
                        ? substr(implode(', ', array_unique($capitalNames)), 0, 255)
                        : null;

                    $region = $normalizeText(data_get($item, 'region'));
                    $region = $region ? substr($region, 0, 255) : null;

                    $latitude = $normalizeNumber(data_get($item, 'coordinates.lat'));
                    $longitude = $normalizeNumber(data_get($item, 'coordinates.lng'));
                    $population = (int) ($normalizeNumber(data_get($item, 'population')) ?? 0);

                    /*
                     * Membaca mata uang, baik berbentuk list maupun object.
                     */
                    $currencies = data_get($item, 'currencies', []);
                    $currencyCode = null;
                    $currencyName = null;

                    if (is_array($currencies) && !empty($currencies)) {
                        if (array_is_list($currencies)) {
                            $firstCurrency = $currencies[0] ?? null;

                            if (is_array($firstCurrency)) {
                                $currencyCode = $normalizeText(
                                    $firstCurrency['code']
                                    ?? $firstCurrency['iso_code']
                                    ?? $firstCurrency['currency_code']
                                    ?? null
                                );

                                $currencyName = $normalizeText(
                                    $firstCurrency['name']
                                    ?? $firstCurrency['currency_name']
                                    ?? null
                                );
                            } elseif (is_string($firstCurrency)) {
                                $currencyCode = $firstCurrency;
                            }
                        } else {
                            $firstCurrencyCode = array_key_first($currencies);
                            $firstCurrency = $currencies[$firstCurrencyCode] ?? null;

                            $currencyCode = is_string($firstCurrencyCode)
                                ? $firstCurrencyCode
                                : null;

                            if (is_array($firstCurrency)) {
                                $currencyCode = $normalizeText(
                                    $firstCurrency['code']
                                    ?? $firstCurrency['iso_code']
                                    ?? $currencyCode
                                );

                                $currencyName = $normalizeText(
                                    $firstCurrency['name']
                                    ?? $firstCurrency['currency_name']
                                    ?? null
                                );
                            } else {
                                $currencyName = $normalizeText($firstCurrency);
                            }
                        }
                    }

                    $currencyCode = $currencyCode
                        ? strtoupper(substr($currencyCode, 0, 10))
                        : null;

                    $currencyName = $currencyName
                        ? substr($currencyName, 0, 255)
                        : null;

                    /*
                     * Membaca seluruh nama bahasa menjadi satu string.
                     */
                    $languagesData = data_get($item, 'languages', []);
                    $languageNames = [];

                    if (is_array($languagesData)) {
                        foreach ($languagesData as $languageKey => $languageItem) {
                            if (is_array($languageItem)) {
                                $languageName = $normalizeText(
                                    $languageItem['name']
                                    ?? $languageItem['english_name']
                                    ?? $languageItem['common']
                                    ?? null
                                );
                            } else {
                                $languageName = $normalizeText($languageItem);
                            }

                            if (!$languageName && is_string($languageKey) && !is_numeric($languageKey)) {
                                $languageName = $languageKey;
                            }

                            if ($languageName) {
                                $languageNames[] = $languageName;
                            }
                        }
                    } else {
                        $languageName = $normalizeText($languagesData);

                        if ($languageName) {
                            $languageNames[] = $languageName;
                        }
                    }

                    $language = !empty($languageNames)
                        ? substr(implode(', ', array_unique($languageNames)), 0, 255)
                        : null;

                    DB::transaction(function () use (
                        $countryCode,
                        $countryName,
                        $capital,
                        $region,
                        $currencyCode,
                        $currencyName,
                        $language,
                        $latitude,
                        $longitude,
                        $population,
                        $now,
                        &$importedCount,
                        &$updatedCount
                    ) {
                        $existingCountry = DB::table('countries')
                            ->where('country_code', $countryCode)
                            ->first();

                        if ($existingCountry) {
                            $countryId = $existingCountry->id;

                            DB::table('countries')
                                ->where('id', $countryId)
                                ->update([
                                    'name' => $countryName,
                                    'capital' => $capital,
                                    'region' => $region,
                                    'currency_code' => $currencyCode,
                                    'currency_name' => $currencyName,
                                    'language' => $language,
                                    'latitude' => $latitude,
                                    'longitude' => $longitude,
                                    'updated_at' => $now,
                                ]);

                            $updatedCount++;
                        } else {
                            $countryId = DB::table('countries')->insertGetId([
                                'country_code' => $countryCode,
                                'name' => $countryName,
                                'capital' => $capital,
                                'region' => $region,
                                'currency_code' => $currencyCode,
                                'currency_name' => $currencyName,
                                'language' => $language,
                                'latitude' => $latitude,
                                'longitude' => $longitude,
                                'created_at' => $now,
                                'updated_at' => $now,
                            ]);

                            $importedCount++;
                        }

                        /*
                         * REST Countries hanya menyediakan populasi, bukan GDP,
                         * inflasi, ekspor, atau impor. Jangan membuat baris tahun
                         * berjalan baru yang berisi 0 karena baris tersebut akan
                         * menutupi data World Bank yang tahun datanya lebih lama.
                         */
                        $existingEconomic = DB::table('economic_indicators')
                            ->where('country_id', $countryId)
                            ->orderByDesc('year')
                            ->first();

                        if (!$existingEconomic) {
                            DB::table('economic_indicators')->insert([
                                'country_id' => $countryId,
                                'year' => (int) now()->format('Y'),
                                'gdp' => 0,
                                'inflation_rate' => 0,
                                'population' => $population,
                                'exports' => 0,
                                'imports' => 0,
                                'created_at' => $now,
                                'updated_at' => $now,
                            ]);
                        } elseif (
                            $population > 0
                            && (float) ($existingEconomic->population ?? 0) <= 0
                        ) {
                            DB::table('economic_indicators')
                                ->where('id', $existingEconomic->id)
                                ->update([
                                    'population' => $population,
                                    'updated_at' => $now,
                                ]);
                        }

                        $existingWeather = DB::table('weather_reports')
                            ->where('country_id', $countryId)
                            ->latest('reported_at')
                            ->first();

                        if (!$existingWeather) {
                            DB::table('weather_reports')->insert([
                                'country_id' => $countryId,
                                'temperature' => 0,
                                'rainfall' => 0,
                                'wind_speed' => 0,
                                'weather_status' => 'Belum sinkron Open-Meteo',
                                'weather_risk_score' => 0,
                                'reported_at' => $now,
                                'created_at' => $now,
                                'updated_at' => $now,
                            ]);
                        }

                        if ($currencyCode) {
                            $existingCurrency = DB::table('currency_rates')
                                ->where('country_id', $countryId)
                                ->latest('rate_date')
                                ->first();

                            if (!$existingCurrency) {
                                DB::table('currency_rates')->insert([
                                    'country_id' => $countryId,
                                    'base_currency' => 'USD',
                                    'target_currency' => $currencyCode,
                                    'exchange_rate' => 0,
                                    'change_percentage' => 0,
                                    'currency_risk_score' => 0,
                                    'rate_date' => now()->toDateString(),
                                    'created_at' => $now,
                                    'updated_at' => $now,
                                ]);
                            } else {
                                DB::table('currency_rates')
                                    ->where('id', $existingCurrency->id)
                                    ->update([
                                        'target_currency' => $currencyCode,
                                        'updated_at' => $now,
                                    ]);
                            }
                        }

                        $existingRisk = DB::table('risk_scores')
                            ->where('country_id', $countryId)
                            ->latest('score_date')
                            ->first();

                        if (!$existingRisk) {
                            DB::table('risk_scores')->insert([
                                'country_id' => $countryId,
                                'weather_score' => 0,
                                'inflation_score' => 0,
                                'currency_score' => 0,
                                'news_score' => 30,
                                'total_score' => 6,
                                'risk_level' => 'Belum Dihitung',
                                'recommendation' => 'Negara berhasil disinkronkan dari REST Countries API. Lakukan sinkronisasi cuaca, kurs, ekonomi, dan berita untuk menghitung risiko aktual.',
                                'score_date' => now()->toDateString(),
                                'created_at' => $now,
                                'updated_at' => $now,
                            ]);
                        }
                    });
                }

                $countThisPage = count($countriesData);
                $total = (int) data_get($meta, 'total', 0);
                $moreFromMeta = data_get($meta, 'more');

                if ($moreFromMeta !== null) {
                    $hasMore = (bool) $moreFromMeta;
                } elseif ($total > 0) {
                    $hasMore = ($offset + $countThisPage) < $total;
                } else {
                    $hasMore = $countThisPage === $limit;
                }

                if ($countThisPage === 0) {
                    $hasMore = false;
                }

                $offset += $limit;
                $page++;
            }

            DB::table('api_logs')->insert([
                'api_name' => 'REST Countries API',
                'endpoint' => $baseUrl,
                'status' => 'Success',
                'message' => 'Sinkronisasi selesai. Negara baru: '
                    . $importedCount
                    . ', diperbarui: '
                    . $updatedCount
                    . ', dilewati: '
                    . $skippedCount
                    . '.',
                'requested_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            return redirect()
                ->route('admin.index')
                ->with(
                    'success',
                    'Sinkronisasi negara selesai. Negara baru: '
                    . $importedCount
                    . ', diperbarui: '
                    . $updatedCount
                    . ', dilewati: '
                    . $skippedCount
                    . '.'
                );
        } catch (\Throwable $exception) {
            DB::table('api_logs')->insert([
                'api_name' => 'REST Countries API',
                'endpoint' => $baseUrl,
                'status' => 'Failed',
                'message' => substr($exception->getMessage(), 0, 1000),
                'requested_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            return redirect()
                ->route('admin.index')
                ->with('error', 'Sinkronisasi negara gagal: ' . $exception->getMessage());
        }
    }
}