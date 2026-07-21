@extends('layouts.app')

@section('title', 'Dashboard Intelijen Risiko Rantai Pasok Global - Supply Chain Risk Intelligence')

@section('content')
    @php
        $freshnessClass = $dataFreshness === 'Data terbaru'
            ? 'risk-low'
            : ($dataFreshness === 'Perlu diperbarui'
                ? 'risk-medium'
                : 'risk-high');

        $formatDateTime = function ($value) {
            return $value
                ? \Carbon\Carbon::parse($value)
                    ->timezone('Asia/Jakarta')
                    ->translatedFormat('d F Y H:i') . ' WIB'
                : '-';
        };

        $riskClass = function ($score) {
            return $score >= 60
                ? 'risk-high'
                : ($score >= 35
                    ? 'risk-medium'
                    : 'risk-low');
        };
    @endphp

    {{-- TOPBAR --}}
    <div class="topbar">
        <div class="page-title">
            <h1>Dashboard Intelijen Risiko Rantai Pasok Global</h1>

            <p>
                Monitoring risiko negara, cuaca, kurs, berita,
                dan pelabuhan dari data sistem.
            </p>
        </div>

        <div class="scm-topbar-tools">
            <div class="scm-search">
                <i class="bi bi-search"></i>

                <input
                    type="search"
                    class="form-control"
                    placeholder="Cari negara yang tersedia..."
                    aria-label="Cari negara"
                    disabled
                >
            </div>

            <span class="risk-badge {{ $freshnessClass }}">
                {{ $dataFreshness }}
            </span>

            <div class="dropdown">
                <button
                    class="profile-chip border-0"
                    type="button"
                    data-bs-toggle="dropdown"
                    aria-expanded="false"
                >
                    <span class="user-avatar">
                        <i class="bi bi-person-fill"></i>
                    </span>

                    <span class="text-start">
                        <span class="d-block fw-bold">
                            {{ auth()->user()->name }}
                        </span>

                        <small class="scm-muted">
                            {{ ucfirst(auth()->user()->role ?? 'user') }}
                        </small>
                    </span>

                    <i class="bi bi-chevron-down scm-muted"></i>
                </button>

                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <form
                            action="{{ route('logout') }}"
                            method="POST"
                        >
                            @csrf

                            <button
                                type="submit"
                                class="dropdown-item"
                            >
                                <i class="bi bi-box-arrow-right me-2"></i>
                                Keluar
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    {{-- DASHBOARD CONTENT --}}
    <div class="dashboard-content">

        {{-- HEADER DATA --}}
        <div class="scm-page-header mb-3">
            <div>
                <div class="scm-muted">
                    Terakhir diperbarui
                </div>

                <strong>
                    {{ $formatDateTime($latestDataAt) }}
                </strong>
            </div>

            <form
                action="{{ route('dashboard') }}"
                method="GET"
            >
                <select
                    name="country_id"
                    class="form-select country-select"
                    onchange="this.form.submit()"
                >
                    @foreach ($countries as $item)
                        <option
                            value="{{ $item->id }}"
                            {{ (string) $selectedCountryId === (string) $item->id ? 'selected' : '' }}
                        >
                            {{ $item->name }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        {{-- KPI --}}
        <div class="row g-3 mb-3">
            <div class="col-xl-3 col-md-6">
                <div class="scm-kpi-card">
                    <div class="scm-kpi-copy">
                        <div class="metric-label">
                            Negara Dipantau
                        </div>

                        <div class="metric-value">
                            {{ number_format($summary['monitored_countries_count'] ?? 0) }}
                        </div>

                        <div class="metric-sub">
                            {{ ($summary['watched_countries_count'] ?? 0) > 0
                                ? 'Negara unik di Watchlist'
                                : 'Fallback data negara tersedia'
                            }}
                        </div>
                    </div>

                    <div class="scm-kpi-icon icon-teal">
                        <i class="bi bi-globe2"></i>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="scm-kpi-card">
                    <div class="scm-kpi-copy">
                        <div class="metric-label">
                            Risiko Tinggi
                        </div>

                        <div class="metric-value">
                            {{ number_format($riskDistribution['high'] ?? 0) }}
                        </div>

                        <div class="metric-sub">
                            Berdasarkan skor terbaru
                        </div>
                    </div>

                    <div class="scm-kpi-icon icon-coral">
                        <i class="bi bi-shield-exclamation"></i>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="scm-kpi-card">
                    <div class="scm-kpi-copy">
                        <div class="metric-label">
                            Gangguan Pengiriman
                        </div>

                        <div class="metric-value">
                            {{ number_format($summary['shipping_disruptions_count'] ?? 0) }}
                        </div>

                        <div class="metric-sub">
                            Pelabuhan Waspada, Siaga, atau Darurat
                        </div>
                    </div>

                    <div class="scm-kpi-icon icon-blue">
                        <i class="bi bi-anchor"></i>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="scm-kpi-card">
                    <div class="scm-kpi-copy">
                        <div class="metric-label">
                            Kurs USD/{{ $currency->target_currency ?? '-' }}
                        </div>

                        <div class="metric-value">
                            {{ number_format((float) ($currency->exchange_rate ?? 0), 2) }}
                        </div>

                        <div class="metric-sub">
                            Perubahan
                            {{ number_format((float) ($currency->change_percentage ?? 0), 2) }}%
                        </div>
                    </div>

                    <div class="scm-kpi-icon icon-green">
                        <i class="bi bi-currency-exchange"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- PETA DAN INSIGHT --}}
        <div class="row g-3 mb-3">

            {{-- PETA --}}
            <div class="col-xl-8">
                <div class="scm-map-card">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <div>
                            <div class="scm-section-title mb-1">
                                Peta Risiko Pelabuhan Global
                            </div>

                            <div class="map-legend">
                                <span>
                                    <span
                                        class="legend-dot"
                                        style="background:#237C7E"
                                    ></span>
                                    Rendah
                                </span>

                                <span>
                                    <span
                                        class="legend-dot"
                                        style="background:#D99A2B"
                                    ></span>
                                    Sedang
                                </span>

                                <span>
                                    <span
                                        class="legend-dot"
                                        style="background:#E76F51"
                                    ></span>
                                    Tinggi
                                </span>
                            </div>
                        </div>

                        <span class="risk-badge risk-low">
                            {{ $allPorts->count() }} marker
                        </span>
                    </div>

                    <div id="map"></div>
                </div>
            </div>

            {{-- INSIGHT DAN RANKING --}}
            <div class="col-xl-4">
               <div class="scm-card h-auto mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="scm-section-title mb-0">
                            Insight Utama
                        </div>

                        <a
                            href="{{ route('news.index', ['country_id' => $selectedCountryId]) }}"
                            class="small fw-bold text-decoration-none"
                        >
                            Lihat Berita
                        </a>
                    </div>

                    @forelse ($dashboardInsights->take(4) as $insight)
                        <a
                            href="{{ $insight['url'] }}"
                            class="insight-item d-flex gap-3 text-decoration-none"
                        >
                            <span
                                class="insight-icon {{
                                    $insight['level'] === 'high'
                                        ? 'risk-high'
                                        : ($insight['level'] === 'medium'
                                            ? 'risk-medium'
                                            : 'risk-low')
                                }}"
                            >
                                <i class="bi {{ $insight['icon'] }}"></i>
                            </span>

                            <span class="d-block">
                                <span class="insight-title">
                                    {{ $insight['title'] }}
                                </span>

                                <span class="insight-desc d-block">
                                    {{ \Illuminate\Support\Str::limit($insight['description'], 105) }}
                                </span>

                                <small class="scm-muted">
                                    {{ $formatDateTime($insight['time']) }}
                                </small>
                            </span>
                        </a>
                    @empty
                        <div class="scm-empty-state">
                            Belum ada insight terbaru dari data sistem.
                        </div>
                    @endforelse
                </div>

               <div class="scm-card h-auto">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="scm-section-title mb-0">
                            Peringkat Risiko Negara
                        </div>

                        <a
                            href="{{ route('risks.index') }}"
                            class="small fw-bold text-decoration-none"
                        >
                            Lihat Semua
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table scm-table align-middle">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Negara</th>
                                <th>Skor</th>
                            </tr>
                            </thead>

                            <tbody>
                            @forelse ($riskRanking as $index => $row)
                                <tr>
                                    <td>
                                        {{ $index + 1 }}
                                    </td>

                                    <td>
                                        <strong>
                                            {{ $row->country_name }}
                                        </strong>

                                        <div class="scm-muted">
                                            {{ $row->risk_level ?? '-' }}
                                        </div>
                                    </td>

                                    <td>
                                        <span
                                            class="risk-badge {{
                                                $riskClass($row->total_score ?? 0)
                                            }}"
                                        >
                                            {{ number_format((float) ($row->total_score ?? 0), 0) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td
                                        colspan="3"
                                        class="text-muted text-center py-3"
                                    >
                                        Belum ada data risiko.
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- GRAFIK DAN RINGKASAN --}}
        <div class="row g-3">

            {{-- TREN RISIKO --}}
            <div class="col-xl-6">
                <div class="scm-chart-card">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <div class="scm-section-title mb-0">
                            Tren Risiko: {{ $country->name ?? '-' }}
                        </div>

                        <span class="risk-badge risk-low">
                            {{ $riskTrend->count() }} data
                        </span>
                    </div>

                    @if ($riskTrend->isNotEmpty())
                        <div class="dashboard-chart-wrap dashboard-trend-chart">
                            <canvas id="dashboardRiskTrendChart"></canvas>
                        </div>
                    @else
                        <div class="scm-empty-state">
                            Data historis risiko belum tersedia.
                        </div>
                    @endif
                </div>
            </div>

            {{-- DISTRIBUSI RISIKO --}}
            <div class="col-xl-3 col-lg-6">
                <div class="scm-chart-card">
                    <div class="scm-section-title">
                        Distribusi Risiko
                    </div>

                    <div class="dashboard-chart-wrap dashboard-doughnut-chart">
                        <canvas id="riskDistributionChart"></canvas>
                    </div>

                    <div class="mt-3 d-grid gap-2">
                        <span class="scm-muted">
                            Rendah:
                            {{ $riskDistribution['low'] ?? 0 }}
                        </span>

                        <span class="scm-muted">
                            Sedang:
                            {{ $riskDistribution['medium'] ?? 0 }}
                        </span>

                        <span class="scm-muted">
                            Tinggi:
                            {{ $riskDistribution['high'] ?? 0 }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- RINGKASAN NEGARA --}}
            <div class="col-xl-3 col-lg-6">
                <div class="scm-card">
                    <div class="scm-section-title">
                        Ringkasan {{ $country->name ?? '-' }}
                    </div>

                    <div class="info-row">
                        <span class="info-label">
                            Skor risiko
                        </span>

                        <span class="info-value">
                            {{ number_format((float) ($risk->total_score ?? 0), 0) }}/100
                        </span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">
                            Status
                        </span>

                        <span class="info-value">
                            {{ $risk->risk_level ?? '-' }}
                        </span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">
                            Cuaca
                        </span>

                        <span class="info-value">
                            {{ $weather->weather_status ?? '-' }}
                        </span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">
                            Berita
                        </span>

                        <span class="info-value">
                            {{ $news->count() }}
                        </span>
                    </div>

                    <div class="recommendation-box mt-3">
                        {{ $risk->recommendation
                            ?? 'Belum ada rekomendasi untuk negara ini.'
                        }}
                    </div>
                </div>
            </div>
        </div>

        {{-- FOOTER --}}
        <div class="footer">
            © {{ date('Y') }} Supply Chain Risk Intelligence. Semua hak dilindungi.
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const riskTrendCanvas =
            document.getElementById('dashboardRiskTrendChart');

        const riskDistributionCanvas =
            document.getElementById('riskDistributionChart');

        /*
         * Grafik tren risiko
         */
        if (riskTrendCanvas) {
            if (
                window.dashboardRiskTrendChart &&
                typeof window.dashboardRiskTrendChart.destroy === 'function'
            ) {
                window.dashboardRiskTrendChart.destroy();
            }

            const trendLabels = @json(
                $riskTrend
                    ->pluck('score_date')
                    ->map(function ($date) {
                        return $date
                            ? \Carbon\Carbon::parse($date)->format('d M Y')
                            : '-';
                    })
                    ->values()
            );

            const trendValues = @json(
                $riskTrend
                    ->pluck('total_score')
                    ->map(function ($score) {
                        return (float) $score;
                    })
                    ->values()
            );

            window.dashboardRiskTrendChart = new Chart(
                riskTrendCanvas,
                {
                    type: 'line',

                    data: {
                        labels: trendLabels,

                        datasets: [
                            {
                                label: 'Skor Risiko',
                                data: trendValues,
                                borderColor: '#237C7E',
                                backgroundColor: 'rgba(35, 124, 126, 0.12)',
                                pointBackgroundColor: '#237C7E',
                                pointBorderColor: '#ffffff',
                                pointBorderWidth: 2,
                                pointRadius: 4,
                                pointHoverRadius: 6,
                                borderWidth: 3,
                                tension: 0.35,
                                fill: true
                            }
                        ]
                    },

                    options: {
                        responsive: true,
                        maintainAspectRatio: false,

                        interaction: {
                            intersect: false,
                            mode: 'index'
                        },

                        plugins: {
                            legend: {
                                display: false
                            },

                            tooltip: {
                                displayColors: false,

                                callbacks: {
                                    label: function (context) {
                                        const value =
                                            Number(context.raw || 0);

                                        return `Skor Risiko: ${value.toFixed(0)}/100`;
                                    }
                                }
                            }
                        },

                        scales: {
                            x: {
                                grid: {
                                    display: false
                                },

                                ticks: {
                                    maxRotation: 0,
                                    autoSkip: true,
                                    maxTicksLimit: 7
                                }
                            },

                            y: {
                                beginAtZero: true,
                                min: 0,
                                max: 100,

                                ticks: {
                                    stepSize: 20
                                },

                                grid: {
                                    color: 'rgba(16, 42, 51, 0.08)'
                                }
                            }
                        }
                    }
                }
            );
        }

        /*
         * Grafik distribusi risiko
         */
        if (riskDistributionCanvas) {
            if (
                window.riskDistributionChart &&
                typeof window.riskDistributionChart.destroy === 'function'
            ) {
                window.riskDistributionChart.destroy();
            }

            window.riskDistributionChart = new Chart(
                riskDistributionCanvas,
                {
                    type: 'doughnut',

                    data: {
                        labels: [
                            'Risiko Rendah',
                            'Risiko Sedang',
                            'Risiko Tinggi'
                        ],

                        datasets: [
                            {
                                data: [
                                    Number(@json($riskDistribution['low'] ?? 0)),
                                    Number(@json($riskDistribution['medium'] ?? 0)),
                                    Number(@json($riskDistribution['high'] ?? 0))
                                ],

                                backgroundColor: [
                                    '#237C7E',
                                    '#D99A2B',
                                    '#E76F51'
                                ],

                                hoverBackgroundColor: [
                                    '#1D6769',
                                    '#C08624',
                                    '#D85E41'
                                ],

                                borderColor: '#ffffff',
                                borderWidth: 3
                            }
                        ]
                    },

                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '64%',

                        plugins: {
                            legend: {
                                display: false
                            },

                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        const value =
                                            Number(context.raw || 0);

                                        return `${context.label}: ${value} negara`;
                                    }
                                }
                            }
                        }
                    }
                }
            );
        }

        /*
         * Peta risiko pelabuhan
         */
        const mapElement = document.getElementById('map');

        if (mapElement && typeof L !== 'undefined') {
            if (window.dashboardPortMap) {
                window.dashboardPortMap.remove();
            }

            window.dashboardPortMap = L.map(
                mapElement,
                {
                    scrollWheelZoom: false
                }
            ).setView([5, 110], 2);

            L.tileLayer(
                'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                {
                    attribution:
                        '&copy; OpenStreetMap contributors'
                }
            ).addTo(window.dashboardPortMap);

            const allPorts = @json($allPorts);

            const escapeHtml = function (value) {
                return String(value ?? '-')
                    .replaceAll('&', '&amp;')
                    .replaceAll('<', '&lt;')
                    .replaceAll('>', '&gt;')
                    .replaceAll('"', '&quot;')
                    .replaceAll("'", '&#039;');
            };

            allPorts.forEach(function (port) {
                const latitude = Number(port.latitude);
                const longitude = Number(port.longitude);

                if (
                    !Number.isFinite(latitude) ||
                    !Number.isFinite(longitude)
                ) {
                    return;
                }

                const score =
                    Number(port.port_risk_score || 0);

                const color =
                    score >= 60
                        ? '#E76F51'
                        : (score >= 35
                            ? '#D99A2B'
                            : '#237C7E');

                L.circleMarker(
                    [latitude, longitude],
                    {
                        radius: score >= 60 ? 7 : 5,
                        color: color,
                        fillColor: color,
                        fillOpacity: 0.78,
                        weight: 2
                    }
                )
                    .addTo(window.dashboardPortMap)
                    .bindPopup(`
                        <strong>${escapeHtml(port.name)}</strong><br>
                        Negara: ${escapeHtml(port.country_name)}<br>
                        Kota: ${escapeHtml(port.city)}<br>
                        Status: ${escapeHtml(port.status)}<br>
                        Risiko: ${score.toFixed(0)}/100
                    `);
            });

            setTimeout(function () {
                window.dashboardPortMap.invalidateSize();
            }, 300);
        }
    });
</script>
@endpush
