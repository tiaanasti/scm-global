<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Mesin Risiko - Supply Chain Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap, Icons, Chart -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ time() }}">
</head>

<body>
<div class="app">
    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="brand">
            <div class="brand-icon">
                <i class="bi bi-box-seam"></i>
            </div>
            <div>
                Supply Chain<br>Management
            </div>
        </div>

        <a href="{{ route('dashboard') }}" class="nav-link-custom">
    <i class="bi bi-house-door-fill"></i>
    Dashboard
</a>

<a href="{{ route('countries.index') }}" class="nav-link-custom">
    <i class="bi bi-globe2"></i>
    Negara
</a>

<a href="{{ route('risks.index') }}" class="nav-link-custom active">
    <i class="bi bi-shield-check"></i>
    Risiko
</a>

<a href="{{ route('ports.index') }}" class="nav-link-custom">
    <i class="bi bi-pin-map-fill"></i>
    Pelabuhan
</a>

<a href="{{ route('currencies.index') }}" class="nav-link-custom {{ request()->routeIs('currencies.index') ? 'active' : '' }}">
    <i class="bi bi-currency-dollar"></i>
    <span>Kurs</span>
</a>

<a href="{{ route('news.index') }}" class="nav-link-custom {{ request()->routeIs('news.index') ? 'active' : '' }}">
    <i class="bi bi-newspaper"></i>
    <span>Berita</span>
</a>

<a href="#" class="nav-link-custom">
    <i class="bi bi-bar-chart-line"></i>
    Perbandingan
</a>

<a href="#" class="nav-link-custom">
    <i class="bi bi-star"></i>
    Watchlist
</a>

<a href="#" class="nav-link-custom">
    <i class="bi bi-person-gear"></i>
    Admin
</a>

<a href="{{ route('ports.index') }}" class="nav-link-custom active">
    <i class="bi bi-anchor"></i>
    Pelabuhan
</a>

        <a href="#" class="nav-link-custom">
            <i class="bi bi-currency-dollar"></i>
            Kurs
        </a>

        <a href="#" class="nav-link-custom">
            <i class="bi bi-newspaper"></i>
            Berita
        </a>

        <a href="#" class="nav-link-custom">
            <i class="bi bi-bar-chart-line"></i>
            Perbandingan
        </a>

        <a href="#" class="nav-link-custom">
            <i class="bi bi-star"></i>
            Watchlist
        </a>

        <a href="#" class="nav-link-custom">
            <i class="bi bi-person-gear"></i>
            Admin
        </a>

        <div class="sidebar-user">
            <div class="user-avatar">
                <i class="bi bi-person-fill"></i>
            </div>
            <div>
                <div style="font-weight: 700;">Admin</div>
                <div style="font-size: 13px; color: #bfdbfe;">Administrator</div>
            </div>
        </div>
    </aside>

    <!-- MAIN -->
    <main class="main">
        <div class="topbar">
            <div class="page-title">
                <h1>Mesin Skor Risiko</h1>
                <p>Analisis risiko rantai pasok berdasarkan cuaca, inflasi, kurs, dan sentimen berita.</p>
            </div>

            <form action="{{ route('risks.index') }}" method="GET">
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
            <!-- TOP CARDS -->
            <div class="row g-3 mb-4">
                <div class="col-lg-3 col-md-6">
                    <div class="card-clean metric-card">
                        <div class="metric-icon icon-red">
                            <i class="bi bi-shield-exclamation"></i>
                        </div>
                        <div>
                            <div class="metric-label">Total Risiko</div>
                            <div class="metric-value">{{ $risk->total_score ?? 0 }}/100</div>
                            <div class="metric-sub">
                                <span class="risk-badge
                                    {{ ($risk->total_score ?? 0) >= 60 ? 'risk-high' : (($risk->total_score ?? 0) >= 35 ? 'risk-medium' : 'risk-low') }}">
                                    {{ $risk->risk_level ?? '-' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card-clean metric-card">
                        <div class="metric-icon icon-blue">
                            <i class="bi bi-cloud-rain"></i>
                        </div>
                        <div>
                            <div class="metric-label">Risiko Cuaca</div>
                            <div class="metric-value">{{ $risk->weather_score ?? 0 }}%</div>
                            <div class="metric-sub">{{ $weather->weather_status ?? '-' }}</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card-clean metric-card">
                        <div class="metric-icon icon-orange">
                            <i class="bi bi-percent"></i>
                        </div>
                        <div>
                            <div class="metric-label">Risiko Inflasi</div>
                            <div class="metric-value">{{ $risk->inflation_score ?? 0 }}%</div>
                            <div class="metric-sub">Inflasi {{ $economic->inflation_rate ?? 0 }}%</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card-clean metric-card">
                        <div class="metric-icon icon-purple">
                            <i class="bi bi-currency-exchange"></i>
                        </div>
                        <div>
                            <div class="metric-label">Risiko Kurs</div>
                            <div class="metric-value">{{ $risk->currency_score ?? 0 }}%</div>
                            <div class="metric-sub">USD/{{ $currency->target_currency ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CHART + FORMULA -->
            <div class="row g-4 mb-4">
                <div class="col-lg-5">
                    <div class="card-clean">
                        <div class="section-title">Komposisi Risiko {{ $country->name ?? '-' }}</div>

                        <canvas id="riskCompositionChart" height="230"></canvas>

                        <div class="risk-score-box mt-3">
                            <div class="risk-number">{{ $risk->total_score ?? 0 }}</div>
                            <div class="metric-sub">Skor Risiko Total</div>
                            <div class="risk-level">{{ $risk->risk_level ?? '-' }}</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="card-clean">
                        <div class="section-title">Model Perhitungan Risiko</div>

                        <div class="recommendation-box mb-4">
                            <strong>Formula:</strong>
                            <div class="mt-2">
                                Total Risiko = Cuaca + Inflasi + Kurs + Berita
                            </div>
                        </div>

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

                        <div class="recommendation-box mt-4">
                            <strong>Interpretasi Sistem:</strong>
                            <div class="mt-2">
                                {{ $risk->recommendation ?? 'Belum ada rekomendasi untuk negara ini.' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- NEWS + TABLE -->
            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="card-clean">
                        <div class="section-title">Berita yang Mempengaruhi Risiko</div>

                        @forelse ($news as $item)
                            <div class="news-item">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="risk-badge
                                        {{ $item->sentiment === 'Negative' ? 'risk-high' : ($item->sentiment === 'Positive' ? 'risk-low' : 'risk-medium') }}">
                                        {{ $item->sentiment }}
                                    </span>
                                    <small class="text-muted">{{ $item->category ?? 'Umum' }}</small>
                                </div>
                                <div class="news-title">{{ $item->title }}</div>
                                <div class="news-desc">{{ $item->description }}</div>
                            </div>
                        @empty
                            <p class="text-muted">Belum ada berita untuk negara ini.</p>
                        @endforelse
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="card-clean">
                        <div class="section-title">Peringkat Risiko Negara</div>

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
                                <tbody>
                                @foreach ($riskRows as $row)
                                    <tr>
                                        <td><strong>{{ $row->country_name }}</strong></td>
                                        <td>{{ $row->weather_score }}%</td>
                                        <td>{{ $row->inflation_score }}%</td>
                                        <td>{{ $row->currency_score }}%</td>
                                        <td>{{ $row->news_score }}%</td>
                                        <td><strong>{{ $row->total_score }}/100</strong></td>
                                        <td>
                                            <span class="risk-badge
                                                {{ $row->total_score >= 60 ? 'risk-high' : ($row->total_score >= 35 ? 'risk-medium' : 'risk-low') }}">
                                                {{ $row->risk_level }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const weatherScore = Number(@json($risk->weather_score ?? 0));
    const inflationScore = Number(@json($risk->inflation_score ?? 0));
    const currencyScore = Number(@json($risk->currency_score ?? 0));
    const newsScore = Number(@json($risk->news_score ?? 0));

    new Chart(document.getElementById('riskCompositionChart'), {
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
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>
</body>
</html>