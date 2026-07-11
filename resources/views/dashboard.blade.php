<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Supply Chain Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Leaflet Map -->
    <link href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ time() }}">
</head>

<body>
<div class="app">

    @include('partials.sidebar')

    <!-- MAIN CONTENT -->
    <main class="main">
        <div class="topbar">
            <div class="page-title">
                <h1>Dashboard Risiko Rantai Pasok Global</h1>
                <p>Monitoring risiko ekonomi, cuaca, kurs, berita, dan pelabuhan secara global.</p>
            </div>

            <form action="{{ route('dashboard') }}" method="GET">
                <select name="country_id" class="form-select country-select" onchange="this.form.submit()">
                    @foreach ($countries as $item)
                        <option value="{{ $item->id }}" {{ $selectedCountryId == $item->id ? 'selected' : '' }}>
                            {{ $item->name }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        <div class="content">
            <!-- LIVE API DATA -->
            <div class="card-clean mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <div class="section-title mb-1">Live Data dari REST API</div>
                        <div class="metric-sub">
                            Data ini dimuat menggunakan JavaScript Fetch API dari endpoint /api/summary dan /api/risk.
                        </div>
                    </div>

                    <span class="risk-badge risk-low" id="apiStatus">
                        Loading API...
                    </span>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-lg-3 col-md-6">
                        <div class="indicator-box indicator-blue">
                            <div class="metric-label">Total Negara</div>
                            <div class="metric-value" id="apiCountriesCount">-</div>
                            <div class="metric-sub">Dari /api/summary</div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="indicator-box indicator-green">
                            <div class="metric-label">Total Pelabuhan</div>
                            <div class="metric-value" id="apiPortsCount">-</div>
                            <div class="metric-sub">Dari /api/summary</div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="indicator-box indicator-orange">
                            <div class="metric-label">Risiko Tinggi</div>
                            <div class="metric-value" id="apiHighRiskCount">-</div>
                            <div class="metric-sub">Negara risiko tinggi</div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="indicator-box indicator-red">
                            <div class="metric-label">Berita Negatif</div>
                            <div class="metric-value" id="apiNegativeNewsCount">-</div>
                            <div class="metric-sub">Sinyal risiko berita</div>
                        </div>
                    </div>
                </div>

                <div class="section-title">Ranking Risiko dari API</div>

                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                        <tr>
                            <th>Negara</th>
                            <th>Cuaca</th>
                            <th>Inflasi</th>
                            <th>Kurs</th>
                            <th>Berita</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody id="apiRiskTable">
                        <tr>
                            <td colspan="7" class="text-muted">Memuat data dari API...</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- METRICS -->
            <div class="row g-3 mb-4">
                <div class="col-xl col-md-4 col-sm-6">
                    <div class="card-clean metric-card">
                        <div class="metric-icon icon-blue">
                            <i class="bi bi-bar-chart-line"></i>
                        </div>
                        <div>
                            <div class="metric-label">GDP</div>
                            <div class="metric-value">
                                ${{ number_format(($economic->gdp ?? 0) / 1000000000000, 2) }}T
                            </div>
                            <div class="metric-sub">Data ekonomi negara</div>
                        </div>
                    </div>
                </div>

                <div class="col-xl col-md-4 col-sm-6">
                    <div class="card-clean metric-card">
                        <div class="metric-icon icon-green">
                            <i class="bi bi-percent"></i>
                        </div>
                        <div>
                            <div class="metric-label">Inflasi</div>
                            <div class="metric-value">{{ $economic->inflation_rate ?? 0 }}%</div>
                            <div class="metric-sub">Perubahan harga tahunan</div>
                        </div>
                    </div>
                </div>

                <div class="col-xl col-md-4 col-sm-6">
                    <div class="card-clean metric-card">
                        <div class="metric-icon icon-blue">
                            <i class="bi bi-cloud-sun"></i>
                        </div>
                        <div>
                            <div class="metric-label">Cuaca Saat Ini</div>
                            <div class="metric-value">{{ $weather->temperature ?? 0 }}°C</div>
                            <div class="metric-sub">{{ $weather->weather_status ?? '-' }}</div>
                        </div>
                    </div>
                </div>

                <div class="col-xl col-md-4 col-sm-6">
                    <div class="card-clean metric-card">
                        <div class="metric-icon icon-purple">
                            <i class="bi bi-currency-exchange"></i>
                        </div>
                        <div>
                            <div class="metric-label">Kurs USD/{{ $currency->target_currency ?? '-' }}</div>
                            <div class="metric-value">
                                {{ number_format($currency->exchange_rate ?? 0, 2) }}
                            </div>
                            <div class="metric-sub">
                                Perubahan {{ $currency->change_percentage ?? 0 }}%
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl col-md-4 col-sm-6">
                    <div class="card-clean metric-card">
                        <div class="metric-icon icon-red">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <div>
                            <div class="metric-label">Skor Risiko</div>
                            <div class="metric-value">
                                {{ $risk->total_score ?? 0 }}<span style="font-size: 16px;">/100</span>
                            </div>
                            <div class="metric-sub" style="color: #f59e0b; font-weight: 700;">
                                {{ $risk->risk_level ?? '-' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CHARTS -->
            <div class="row g-4 mb-4">
                <div class="col-lg-7">
                    <div class="card-clean">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="section-title mb-0">Tren Risiko</div>
                            <select class="form-select form-select-sm" style="width: 170px;">
                                <option>6 Bulan Terakhir</option>
                                <option>12 Bulan Terakhir</option>
                            </select>
                        </div>

                        <canvas id="trendChart" height="135"></canvas>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="card-clean">
                        <div class="section-title">Komponen Risiko</div>

                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <canvas id="riskChart" height="190"></canvas>

                                <div class="risk-score-box mt-2">
                                    <div class="risk-number">{{ $risk->total_score ?? 0 }}</div>
                                    <div style="color: #64748b;">/100</div>
                                    <div class="risk-level">{{ $risk->risk_level ?? '-' }}</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="risk-row">
                                    <div class="risk-row-top">
                                        <span><i class="bi bi-circle-fill text-primary"></i> Cuaca</span>
                                        <strong>{{ $risk->weather_score ?? 0 }}%</strong>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar bg-primary" style="width: {{ $risk->weather_score ?? 0 }}%"></div>
                                    </div>
                                </div>

                                <div class="risk-row">
                                    <div class="risk-row-top">
                                        <span><i class="bi bi-circle-fill text-success"></i> Inflasi</span>
                                        <strong>{{ $risk->inflation_score ?? 0 }}%</strong>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar bg-success" style="width: {{ $risk->inflation_score ?? 0 }}%"></div>
                                    </div>
                                </div>

                                <div class="risk-row">
                                    <div class="risk-row-top">
                                        <span><i class="bi bi-circle-fill text-warning"></i> Kurs</span>
                                        <strong>{{ $risk->currency_score ?? 0 }}%</strong>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar bg-warning" style="width: {{ $risk->currency_score ?? 0 }}%"></div>
                                    </div>
                                </div>

                                <div class="risk-row">
                                    <div class="risk-row-top">
                                        <span><i class="bi bi-circle-fill" style="color:#8b5cf6;"></i> Berita</span>
                                        <strong>{{ $risk->news_score ?? 0 }}%</strong>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar" style="width: {{ $risk->news_score ?? 0 }}%; background:#8b5cf6;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- MAP AND COUNTRY INFO -->
            <div class="row g-4 mb-4">
                <div class="col-lg-7">
                    <div class="card-clean">
                        <div class="section-title">Peta Pelabuhan Global</div>
                        <div id="map"></div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="card-clean">
                        <div class="section-title">Ringkasan Negara: {{ $country->name ?? '-' }}</div>

                        <table class="table table-borderless">
                            <tr>
                                <td class="text-muted">Ibu Kota</td>
                                <td class="fw-bold text-end">{{ $country->capital ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Wilayah</td>
                                <td class="fw-bold text-end">{{ $country->region ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Mata Uang</td>
                                <td class="fw-bold text-end">{{ $country->currency_code ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Populasi</td>
                                <td class="fw-bold text-end">
                                    {{ number_format($economic->population ?? 0) }}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Ekspor</td>
                                <td class="fw-bold text-end">
                                    ${{ number_format(($economic->exports ?? 0) / 1000000000, 2) }}B
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Impor</td>
                                <td class="fw-bold text-end">
                                    ${{ number_format(($economic->imports ?? 0) / 1000000000, 2) }}B
                                </td>
                            </tr>
                        </table>

                        <div class="recommendation-box mt-3">
                            <div class="recommendation-icon">
                                <i class="bi bi-shield-check"></i>
                            </div>
                            <strong>Rekomendasi Sistem</strong>
                            <p class="mt-2 mb-0">
                                {{ $risk->recommendation ?? 'Belum ada rekomendasi untuk negara ini.' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- NEWS AND PORTS -->
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="card-clean">
                        <div class="section-title">Berita Terbaru</div>

                        @forelse ($news as $item)
                            <div class="news-item d-flex gap-3">
                                <div class="news-icon">
                                    @if ($item->category === 'Logistik')
                                        <i class="bi bi-cloud-rain"></i>
                                    @elseif ($item->category === 'Ekonomi')
                                        <i class="bi bi-graph-up-arrow"></i>
                                    @else
                                        <i class="bi bi-newspaper"></i>
                                    @endif
                                </div>

                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="badge-soft
                                            {{ $item->sentiment === 'Negative' ? 'badge-high' : ($item->sentiment === 'Positive' ? 'badge-low' : 'badge-medium') }}">
                                            {{ $item->category ?? 'Umum' }}
                                        </span>

                                        <small class="text-muted">{{ $item->sentiment }}</small>
                                    </div>

                                    <div class="news-title">{{ $item->title }}</div>
                                    <div class="news-desc">{{ $item->description }}</div>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted mb-0">Belum ada berita untuk negara ini.</p>
                        @endforelse
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card-clean">
                        <div class="section-title">Pelabuhan Utama</div>

                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                <tr>
                                    <th>Pelabuhan</th>
                                    <th>Kota</th>
                                    <th>Status</th>
                                    <th>Risiko</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse ($ports as $port)
                                    <tr>
                                        <td>
                                            <i class="bi bi-pin-map-fill text-primary"></i>
                                            <strong>{{ $port->name }}</strong>
                                        </td>
                                        <td>{{ $port->city ?? '-' }}</td>
                                        <td>
                                            <span class="badge-soft
                                                {{ $port->status === 'Aman' ? 'badge-low' : ($port->status === 'Waspada' ? 'badge-high' : 'badge-medium') }}">
                                                {{ $port->status }}
                                            </span>
                                        </td>
                                        <td>{{ $port->port_risk_score }}/100</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-muted">Belum ada data pelabuhan.</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="footer">
                © {{ date('Y') }} Supply Chain Management. Semua hak dilindungi.
            </div>
        </div>
    </main>
</div>

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="{{ asset('js/api-dashboard.js') }}?v={{ time() }}"></script>

<script>
    const riskScore = Number(@json($risk->total_score ?? 0));
    const weatherScore = Number(@json($risk->weather_score ?? 0));
    const inflationScore = Number(@json($risk->inflation_score ?? 0));
    const currencyScore = Number(@json($risk->currency_score ?? 0));
    const newsScore = Number(@json($risk->news_score ?? 0));

    new Chart(document.getElementById('trendChart'), {
        type: 'line',
        data: {
            labels: ['Des', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei'],
            datasets: [{
                label: 'Skor Risiko',
                data: [
                    Math.max(riskScore - 7, 0),
                    Math.max(riskScore - 3, 0),
                    riskScore,
                    Math.min(riskScore + 9, 100),
                    Math.min(riskScore + 3, 100),
                    riskScore
                ],
                borderColor: '#2563eb',
                backgroundColor: 'rgba(37, 99, 235, 0.10)',
                pointBackgroundColor: '#2563eb',
                pointRadius: 4,
                borderWidth: 3,
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });

    new Chart(document.getElementById('riskChart'), {
        type: 'doughnut',
        data: {
            labels: ['Cuaca', 'Inflasi', 'Kurs', 'Berita'],
            datasets: [{
                data: [weatherScore, inflationScore, currencyScore, newsScore],
                backgroundColor: ['#2563eb', '#22c55e', '#f59e0b', '#8b5cf6'],
                borderWidth: 0
            }]
        },
        options: {
            cutout: '68%',
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    const map = L.map('map').setView([5, 110], 2);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: 'OpenStreetMap'
    }).addTo(map);

    const allPorts = @json($allPorts);

    allPorts.forEach(function(port) {
        if (port.latitude && port.longitude) {
            L.marker([port.latitude, port.longitude])
                .addTo(map)
                .bindPopup(`
                    <strong>${port.name}</strong><br>
                    Negara: ${port.country_name}<br>
                    Kota: ${port.city ?? '-'}<br>
                    Status: ${port.status}<br>
                    Risiko: ${port.port_risk_score}/100
                `);
        }
    });

    setTimeout(function () {
        map.invalidateSize();
    }, 300);
</script>

</body>
</html>