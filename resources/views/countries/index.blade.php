@extends('layouts.app')

@section('title', 'Intelijen Negara - Supply Chain Management')

@section('content')
@php
    $formatUsdCompact = static function ($value): string {
        $value = (float) $value;

        if ($value <= 0) {
            return 'Belum tersedia';
        }

        if ($value >= 1000000000000) {
            return '$' . number_format($value / 1000000000000, 2) . 'T';
        }

        if ($value >= 1000000000) {
            return '$' . number_format($value / 1000000000, 2) . 'B';
        }

        if ($value >= 1000000) {
            return '$' . number_format($value / 1000000, 2) . 'M';
        }

        if ($value >= 1000) {
            return '$' . number_format($value / 1000, 2) . 'K';
        }

        return '$' . number_format($value, 2);
    };
@endphp
<style>
    .country-page .topbar {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 20px;
        flex-wrap: wrap;
    }

    .country-page .country-filter {
        min-width: 250px;
    }

    .country-page .summary-card,
    .country-page .section-card {
        height: 100%;
    }

    .country-page .summary-card {
        min-height: 132px;
    }

    .country-page .summary-card > div:last-child {
        min-width: 0;
    }

    .country-page .summary-card .metric-value {
        overflow-wrap: normal;
        word-break: normal;
        hyphens: none;
        font-size: clamp(22px, 2vw, 30px);
    }

    .country-page .profile-value {
        max-width: 62%;
        text-align: right;
        overflow-wrap: anywhere;
    }

    .country-page .indicator-box {
        height: 100%;
        min-height: 128px;
    }

    .country-page .weather-grid .indicator-box {
        min-height: 118px;
    }

    .country-page .recommendation-card {
        border-left: 4px solid #2563eb;
        background: #eff6ff;
    }

    .country-page .recommendation-card p {
        margin: 8px 0 0;
        color: #1e3a8a;
        line-height: 1.65;
    }

    .country-page .ports-card,
    .country-page .news-card {
        height: 100%;
        min-height: 390px;
    }

    .country-page .news-scroll {
        max-height: 390px;
        overflow-y: auto;
        padding-right: 6px;
    }

    .country-page .country-news-item {
        padding: 15px 0;
        border-bottom: 1px solid #e5e7eb;
    }

    .country-page .country-news-item:first-child {
        padding-top: 4px;
    }

    .country-page .country-news-item:last-child {
        border-bottom: 0;
        padding-bottom: 0;
    }

    .country-page .country-news-title {
        display: inline-block;
        color: #0f274c;
        font-weight: 700;
        line-height: 1.45;
        overflow-wrap: anywhere;
    }

    .country-page .country-news-description {
        margin-top: 7px;
        color: #64748b;
        font-size: 14px;
        line-height: 1.6;
        overflow-wrap: anywhere;
        display: -webkit-box;
        -webkit-line-clamp: 4;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .country-page .empty-state {
        padding: 28px 16px;
        text-align: center;
        color: #64748b;
    }

    .country-page .countries-table-wrapper {
        max-height: 580px;
        overflow: auto;
    }

    .country-page .countries-table-wrapper thead th {
        position: sticky;
        top: 0;
        z-index: 2;
        background: #f8fafc;
        white-space: nowrap;
    }

    .country-page .countries-table-wrapper td {
        vertical-align: middle;
        white-space: nowrap;
    }

    .country-page .search-box {
        max-width: 330px;
        width: 100%;
    }

    @media (max-width: 991.98px) {
        .country-page .country-filter,
        .country-page .topbar form {
            width: 100%;
            min-width: 0;
        }

        .country-page .ports-card,
        .country-page .news-card {
            min-height: auto;
        }
    }

    @media (max-width: 575.98px) {
        .country-page .profile-value {
            max-width: 55%;
        }

        .country-page .search-box {
            max-width: none;
        }
    }
</style>

<div class="country-page">
    <div class="topbar">
        <div class="page-title">
            <h1>Intelijen Negara</h1>
            <p>Profil negara untuk memantau ekonomi, cuaca, kurs, pelabuhan, dan risiko rantai pasok.</p>
        </div>

        <form action="{{ route('countries.index') }}" method="GET">
            <select
                name="country_id"
                class="form-select country-filter"
                onchange="this.form.submit()"
            >
                @forelse ($countries as $item)
                    <option
                        value="{{ $item->id }}"
                        {{ (string) $selectedCountryId === (string) $item->id ? 'selected' : '' }}
                    >
                        {{ $item->name }}
                    </option>
                @empty
                    <option value="">Belum ada negara</option>
                @endforelse
            </select>
        </form>
    </div>

    <div class="content">
        <!-- RINGKASAN UTAMA -->
        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card-clean metric-card summary-card">
                    <div class="metric-icon icon-blue">
                        <i class="bi bi-flag"></i>
                    </div>

                    <div style="min-width: 0;">
                        <div class="metric-label">Negara Dipilih</div>
                        <div class="metric-value">{{ data_get($selectedCountry, 'name', '-') }}</div>
                        <div class="metric-sub">{{ data_get($selectedCountry, 'region', '-') }}</div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card-clean metric-card summary-card">
                    <div class="metric-icon icon-green">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>

                    <div style="min-width: 0;">
                        <div class="metric-label">GDP</div>
                        <div class="metric-value">
                            {{ $formatUsdCompact(data_get($economic, 'gdp', 0)) }}
                        </div>
                        <div class="metric-sub">Produk domestik bruto</div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card-clean metric-card summary-card">
                    <div class="metric-icon icon-orange">
                        <i class="bi bi-percent"></i>
                    </div>

                    <div style="min-width: 0;">
                        <div class="metric-label">Inflasi</div>
                        <div class="metric-value">
                            {{ number_format((float) data_get($economic, 'inflation_rate', 0), 2) }}%
                        </div>
                        <div class="metric-sub">Tekanan harga tahunan</div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card-clean metric-card summary-card">
                    <div class="metric-icon icon-red">
                        <i class="bi bi-shield-exclamation"></i>
                    </div>

                    @php
                        $selectedRiskScore = (float) data_get($risk, 'total_score', 0);
                        $selectedRiskClass = $selectedRiskScore >= 60
                            ? 'risk-high'
                            : ($selectedRiskScore >= 35 ? 'risk-medium' : 'risk-low');
                    @endphp

                    <div style="min-width: 0;">
                        <div class="metric-label">Skor Risiko</div>
                        <div class="metric-value">{{ number_format($selectedRiskScore, 0) }}/100</div>
                        <div class="metric-sub mt-1">
                            <span class="risk-badge {{ $selectedRiskClass }}">
                                {{ data_get($risk, 'risk_level', 'Belum Dihitung') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- PROFIL DAN INDIKATOR -->
        <div class="row g-4 mb-4 align-items-stretch">
            <div class="col-lg-5">
                <div class="card-clean section-card">
                    <div class="section-title">Profil Negara</div>

                    <div class="info-row">
                        <div class="info-label">Ibu Kota</div>
                        <div class="info-value profile-value">{{ data_get($selectedCountry, 'capital', '-') }}</div>
                    </div>

                    <div class="info-row">
                        <div class="info-label">Wilayah</div>
                        <div class="info-value profile-value">{{ data_get($selectedCountry, 'region', '-') }}</div>
                    </div>

                    <div class="info-row">
                        <div class="info-label">Kode Negara</div>
                        <div class="info-value profile-value">{{ data_get($selectedCountry, 'country_code', '-') }}</div>
                    </div>

                    <div class="info-row">
                        <div class="info-label">Mata Uang</div>
                        <div class="info-value profile-value">
                            {{ data_get($selectedCountry, 'currency_code', '-') }}
                            @if (data_get($selectedCountry, 'currency_name'))
                                — {{ data_get($selectedCountry, 'currency_name') }}
                            @endif
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="info-label">Bahasa</div>
                        <div class="info-value profile-value">{{ data_get($selectedCountry, 'language', '-') }}</div>
                    </div>

                    <div class="info-row">
                        <div class="info-label">Populasi</div>
                        <div class="info-value profile-value">
                            {{ number_format((float) data_get($economic, 'population', 0)) }}
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="info-label">Koordinat</div>
                        <div class="info-value profile-value">
                            @if (data_get($selectedCountry, 'latitude') !== null && data_get($selectedCountry, 'longitude') !== null)
                                {{ number_format((float) data_get($selectedCountry, 'latitude'), 4) }},
                                {{ number_format((float) data_get($selectedCountry, 'longitude'), 4) }}
                            @else
                                -
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card-clean section-card">
                    <div class="section-title">Indikator Rantai Pasok</div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="indicator-box indicator-blue">
                                <div class="metric-label">Cuaca Saat Ini</div>
                                <div class="metric-value">
                                    {{ number_format((float) data_get($weather, 'temperature', 0), 2) }}°C
                                </div>
                                <div class="metric-sub">
                                    {{ data_get($weather, 'weather_status', 'Belum tersinkron') }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="indicator-box indicator-purple">
                                <div class="metric-label">
                                    Kurs USD/{{ data_get($currency, 'target_currency', data_get($selectedCountry, 'currency_code', '-')) }}
                                </div>
                                <div class="metric-value">
                                    {{ number_format((float) data_get($currency, 'exchange_rate', 0), 4) }}
                                </div>
                                <div class="metric-sub">
                                    Perubahan {{ number_format((float) data_get($currency, 'change_percentage', 0), 2) }}%
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="indicator-box indicator-green">
                                <div class="metric-label">Ekspor</div>
                                <div class="metric-value">
                                    ${{ number_format((float) data_get($economic, 'exports', 0) / 1000000000, 2) }}B
                                </div>
                                <div class="metric-sub">Nilai ekspor tahunan</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="indicator-box indicator-orange">
                                <div class="metric-label">Impor</div>
                                <div class="metric-value">
                                    ${{ number_format((float) data_get($economic, 'imports', 0) / 1000000000, 2) }}B
                                </div>
                                <div class="metric-sub">Nilai impor tahunan</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- DETAIL CUACA -->
        <div class="card-clean mb-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                <div class="section-title mb-0">Detail Sinkronisasi Cuaca</div>
                <span class="risk-badge risk-low">Open-Meteo API</span>
            </div>

            <div class="row g-3 weather-grid">
                <div class="col-xl-3 col-md-6">
                    <div class="indicator-box indicator-blue">
                        <div class="metric-label">Suhu</div>
                        <div class="metric-value">
                            {{ number_format((float) data_get($weather, 'temperature', 0), 2) }}°C
                        </div>
                        <div class="metric-sub">{{ data_get($weather, 'weather_status', '-') }}</div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="indicator-box indicator-green">
                        <div class="metric-label">Curah Hujan</div>
                        <div class="metric-value">
                            {{ number_format((float) data_get($weather, 'rainfall', 0), 2) }} mm
                        </div>
                        <div class="metric-sub">Precipitation</div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="indicator-box indicator-orange">
                        <div class="metric-label">Kecepatan Angin</div>
                        <div class="metric-value">
                            {{ number_format((float) data_get($weather, 'wind_speed', 0), 2) }}
                        </div>
                        <div class="metric-sub">km/jam</div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="indicator-box indicator-purple">
                        <div class="metric-label">Terakhir Sinkron</div>
                        <div class="info-value mt-2" style="font-size: 15px;">
                            @if (data_get($weather, 'reported_at'))
                                {{ \Carbon\Carbon::parse(data_get($weather, 'reported_at'))
                                    ->timezone('Asia/Jakarta')
                                    ->format('d M Y H:i') }} WIB
                            @else
                                -
                            @endif
                        </div>
                        <div class="metric-sub">Waktu pembaruan data</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- REKOMENDASI -->
        <div class="card-clean recommendation-card mb-4">
            <div class="d-flex gap-3 align-items-start">
                <div class="metric-icon icon-blue flex-shrink-0">
                    <i class="bi bi-shield-check"></i>
                </div>

                <div>
                    <strong>Rekomendasi Sistem</strong>
                    <p>
                        {{ data_get(
                            $risk,
                            'recommendation',
                            'Belum ada rekomendasi untuk negara ini. Sinkronkan data cuaca, ekonomi, kurs, dan berita terlebih dahulu.'
                        ) }}
                    </p>
                </div>
            </div>
        </div>

        <!-- TREN EKONOMI -->
        <div class="row g-4 mb-4">
            <div class="col-lg-6">
                <div class="card-clean section-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="section-title mb-0">Tren GDP per Tahun</div>
                        <span class="risk-badge risk-low">{{ $gdpTrend->count() }} data</span>
                    </div>

                    @if ($gdpTrend->isNotEmpty())
                        <canvas id="countryGdpTrendChart" height="120"></canvas>
                    @else
                        <p class="text-muted mb-0">Data historis belum tersedia</p>
                    @endif
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card-clean section-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="section-title mb-0">Tren Inflasi per Tahun</div>
                        <span class="risk-badge risk-low">{{ $inflationTrend->count() }} data</span>
                    </div>

                    @if ($inflationTrend->isNotEmpty())
                        <canvas id="countryInflationTrendChart" height="120"></canvas>
                    @else
                        <p class="text-muted mb-0">Data historis belum tersedia</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- PELABUHAN DAN BERITA -->
        <div class="row g-4 mb-4 align-items-stretch">
            <div class="col-lg-6">
                <div class="card-clean ports-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="section-title mb-0">Pelabuhan Terkait</div>
                        <span class="risk-badge risk-low">
                            @if ($isLandlocked)
                                Tanpa pantai
                            @else
                                {{ $portsCount }} data
                            @endif
                        </span>
                    </div>

                    @if ($isLandlocked)
                        <div class="empty-state py-5 text-center">
                            <i class="bi bi-pin-map fs-3 d-block mb-2"></i>
                            Negara ini tidak memiliki pelabuhan laut karena merupakan negara tanpa garis pantai.
                        </div>
                    @elseif ($portsCount === 0)
                        <div class="empty-state py-5 text-center">
                            <i class="bi bi-pin-map fs-3 d-block mb-2"></i>
                            Data pelabuhan untuk negara ini belum tersedia. Lakukan sinkronisasi World Port Index melalui Panel Admin.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead>
                                <tr>
                                    <th>Pelabuhan</th>
                                    <th>Kota</th>
                                    <th>Status</th>
                                    <th>Risiko</th>
                                </tr>
                                </thead>

                                <tbody>
                                @foreach ($ports as $port)
                                    @php
                                        $portStatusClass = in_array($port->status, ['Aman', 'Normal'], true)
                                            ? 'risk-low'
                                            : ($port->status === 'Waspada' ? 'risk-medium' : 'risk-high');
                                    @endphp

                                    <tr>
                                        <td>
                                            <i class="bi bi-pin-map-fill text-primary me-1"></i>
                                            <strong>{{ $port->name }}</strong>
                                        </td>
                                        <td>{{ $port->city ?? '-' }}</td>
                                        <td>
                                            <span class="risk-badge {{ $portStatusClass }}">
                                                {{ $port->status ?? 'Normal' }}
                                            </span>
                                        </td>
                                        <td>{{ number_format((float) ($port->port_risk_score ?? 0), 0) }}/100</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if ($portsCount > 5)
                            <div class="mt-3 text-muted small">
                                Menampilkan 5 pelabuhan terbaru dari total {{ $portsCount }} pelabuhan.
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card-clean news-card">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="section-title mb-0">Berita Negara</div>
                        <span class="risk-badge risk-low">{{ $news->count() }} berita</span>
                    </div>

                    <div class="news-scroll">
                        @forelse ($news as $item)
                            @php
                                $newsSentimentClass = $item->sentiment === 'Negative'
                                    ? 'risk-high'
                                    : ($item->sentiment === 'Positive' ? 'risk-low' : 'risk-medium');
                            @endphp

                            <article class="country-news-item">
                                <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                                    <span class="risk-badge {{ $newsSentimentClass }}">
                                        {{ $item->sentiment ?? 'Neutral' }}
                                    </span>

                                    <small class="text-muted">
                                        {{ $item->source ?? ($item->category ?? 'Umum') }}
                                    </small>
                                </div>

                                @if (!empty($item->url) && $item->url !== '#')
                                    <a
                                        href="{{ $item->url }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="country-news-title text-decoration-none"
                                    >
                                        {{ $item->title }}
                                        <i class="bi bi-box-arrow-up-right ms-1"></i>
                                    </a>
                                @else
                                    <div class="country-news-title">{{ $item->title }}</div>
                                @endif

                                <div class="country-news-description">
                                    {{ $item->description ?: 'Tidak ada ringkasan berita.' }}
                                </div>
                            </article>
                        @empty
                            <div class="empty-state">
                                <i class="bi bi-newspaper fs-3 d-block mb-2"></i>
                                Belum ada berita untuk negara ini.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- DAFTAR SEMUA NEGARA -->
        <div class="card-clean">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
                <div>
                    <div class="section-title mb-1">Daftar Semua Negara</div>
                    <div class="metric-sub">
                        Menampilkan {{ $countryRows->count() }} negara yang tersimpan di sistem.
                    </div>
                </div>

                <div class="input-group search-box">
                    <span class="input-group-text bg-white">
                        <i class="bi bi-search"></i>
                    </span>
                    <input
                        type="search"
                        id="countryTableSearch"
                        class="form-control"
                        placeholder="Cari negara atau wilayah..."
                    >
                </div>
            </div>

            <div class="countries-table-wrapper">
                <table class="table align-middle mb-0" id="countriesTable">
                    <thead>
                    <tr>
                        <th>Negara</th>
                        <th>Wilayah</th>
                        <th>Mata Uang</th>
                        <th>GDP</th>
                        <th>Inflasi</th>
                        <th>Populasi</th>
                        <th>Skor Risiko</th>
                        <th>Status</th>
                    </tr>
                    </thead>

                    <tbody>
                    @forelse ($countryRows as $row)
                        @php
                            $rowRiskScore = (float) ($row->total_score ?? 0);
                            $rowRiskClass = $rowRiskScore >= 60
                                ? 'risk-high'
                                : ($rowRiskScore >= 35 ? 'risk-medium' : 'risk-low');
                        @endphp

                        <tr>
                            <td>
                                <a
                                    href="{{ route('countries.index', ['country_id' => $row->id]) }}"
                                    class="text-decoration-none fw-bold"
                                >
                                    {{ $row->name }}
                                </a>
                            </td>
                            <td>{{ $row->region ?? '-' }}</td>
                            <td>{{ $row->currency_code ?? '-' }}</td>
                            <td>{{ $formatUsdCompact($row->gdp ?? 0) }}</td>
                            <td>{{ number_format((float) ($row->inflation_rate ?? 0), 2) }}%</td>
                            <td>{{ number_format((float) ($row->population ?? 0)) }}</td>
                            <td>{{ number_format($rowRiskScore, 0) }}/100</td>
                            <td>
                                <span class="risk-badge {{ $rowRiskClass }}">
                                    {{ $row->risk_level ?? 'Belum Dihitung' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="empty-state">Belum ada data negara.</div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="footer">
            © {{ date('Y') }} Supply Chain Management. Semua hak dilindungi.
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('countryTableSearch');
        const tableRows = document.querySelectorAll('#countriesTable tbody tr');

        if (searchInput) {
            searchInput.addEventListener('input', function () {
                const keyword = this.value.toLowerCase().trim();

                tableRows.forEach(function (row) {
                    const rowText = row.textContent.toLowerCase();
                    row.style.display = rowText.includes(keyword) ? '' : 'none';
                });
            });
        }

        @if ($gdpTrend->isNotEmpty())
        const gdpTrendLabels = @json($gdpTrend->pluck('year')->map(fn ($year) => (string) $year));
        const gdpTrendValues = @json($gdpTrend->pluck('gdp')->map(fn ($value) => (float) $value));

        new Chart(document.getElementById('countryGdpTrendChart'), {
            type: 'line',
            data: {
                labels: gdpTrendLabels,
                datasets: [{
                    label: 'GDP (USD)',
                    data: gdpTrendValues,
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.10)',
                    pointBackgroundColor: '#2563eb',
                    pointRadius: gdpTrendValues.length === 1 ? 6 : 4,
                    borderWidth: 3,
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: { legend: { display: true } },
                scales: {
                    x: {
                        offset: gdpTrendValues.length === 1
                    },
                    y: {
                        suggestedMin: gdpTrendValues.length > 1
                            ? Math.min(...gdpTrendValues) * 0.92
                            : undefined,
                        ticks: {
                            callback: function (value) {
                                if (value >= 1000000000000) {
                                    return '$' + (value / 1000000000000).toFixed(1) + 'T';
                                }
                                if (value >= 1000000000) {
                                    return '$' + (value / 1000000000).toFixed(1) + 'B';
                                }
                                return '$' + value;
                            }
                        }
                    }
                }
            }
        });
        @endif

        @if ($inflationTrend->isNotEmpty())
        const inflationTrendLabels = @json($inflationTrend->pluck('year')->map(fn ($year) => (string) $year));
        const inflationTrendValues = @json($inflationTrend->pluck('inflation_rate')->map(fn ($value) => (float) $value));

        new Chart(document.getElementById('countryInflationTrendChart'), {
            type: 'line',
            data: {
                labels: inflationTrendLabels,
                datasets: [{
                    label: 'Inflasi (%)',
                    data: inflationTrendValues,
                    borderColor: '#f59e0b',
                    backgroundColor: 'rgba(245, 158, 11, 0.10)',
                    pointBackgroundColor: '#f59e0b',
                    pointRadius: inflationTrendValues.length === 1 ? 6 : 4,
                    borderWidth: 3,
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: { legend: { display: true } },
                scales: {
                    x: {
                        offset: inflationTrendValues.length === 1
                    },
                    y: {
                        suggestedMin: inflationTrendValues.length > 1
                            ? Math.min(...inflationTrendValues) - 0.5
                            : undefined,
                        ticks: {
                            callback: function (value) {
                                return value + '%';
                            }
                        }
                    }
                }
            }
        });
        @endif
    });
</script>
@endpush