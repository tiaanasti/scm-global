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
}