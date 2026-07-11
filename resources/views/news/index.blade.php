<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sinyal Berita - Supply Chain Management</title>
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
   <body>
<div class="app">

    @include('partials.sidebar')

    <!-- MAIN -->
    <main class="main">
        <div class="topbar">
            <div class="page-title">
                <h1>Sinyal Berita</h1>
                <p>Analisis berita logistik, ekonomi, perdagangan, dan sentimen risiko rantai pasok.</p>
            </div>

            <form action="{{ route('news.index') }}" method="GET">
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
                        <div class="metric-icon icon-blue">
                            <i class="bi bi-newspaper"></i>
                        </div>
                        <div>
                            <div class="metric-label">Total Berita</div>
                            <div class="metric-value">{{ $newsItems->count() }}</div>
                            <div class="metric-sub">{{ $country->name ?? '-' }}</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card-clean metric-card">
                        <div class="metric-icon icon-green">
                            <i class="bi bi-emoji-smile"></i>
                        </div>
                        <div>
                            <div class="metric-label">Sentimen Positif</div>
                            <div class="metric-value">{{ $sentimentSummary['positive_percentage'] }}%</div>
                            <div class="metric-sub">{{ $sentimentSummary['positive_count'] }} berita</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card-clean metric-card">
                        <div class="metric-icon icon-orange">
                            <i class="bi bi-dash-circle"></i>
                        </div>
                        <div>
                            <div class="metric-label">Sentimen Netral</div>
                            <div class="metric-value">{{ $sentimentSummary['neutral_percentage'] }}%</div>
                            <div class="metric-sub">{{ $sentimentSummary['neutral_count'] }} berita</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card-clean metric-card">
                        <div class="metric-icon icon-red">
                            <i class="bi bi-exclamation-triangle"></i>
                        </div>
                        <div>
                            <div class="metric-label">Sentimen Negatif</div>
                            <div class="metric-value">{{ $sentimentSummary['negative_percentage'] }}%</div>
                            <div class="metric-sub">{{ $sentimentSummary['negative_count'] }} berita</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CHART + KEYWORDS -->
            <div class="row g-4 mb-4">
                <div class="col-lg-5">
                    <div class="card-clean">
                        <div class="section-title">Ringkasan Sentimen {{ $country->name ?? '-' }}</div>

                        <canvas id="sentimentChart" height="220"></canvas>

                        <div class="recommendation-box mt-4">
                            <strong>Interpretasi:</strong>
                            <div class="mt-2">
                                Sentimen berita digunakan untuk melihat potensi gangguan rantai pasok dari aspek ekonomi, logistik, perdagangan, dan isu geopolitik.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="card-clean">
                        <div class="section-title">Kamus Sentimen Berita</div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="indicator-box indicator-green">
                                    <div class="metric-label">Kata Positif</div>
                                    <div class="mt-2">
                                        @foreach ($positiveWords as $word)
                                            <span class="risk-badge risk-low me-1 mb-2">
                                                {{ $word->word }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="indicator-box indicator-orange">
                                    <div class="metric-label">Kata Negatif</div>
                                    <div class="mt-2">
                                        @foreach ($negativeWords as $word)
                                            <span class="risk-badge risk-high me-1 mb-2">
                                                {{ $word->word }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="recommendation-box mt-4">
                            <strong>Metode:</strong>
                            <div class="mt-2">
                                Sistem memakai pendekatan lexicon-based sentiment analysis, yaitu menghitung kata positif dan negatif dari berita untuk menentukan kecenderungan sentimen.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SELECTED COUNTRY NEWS -->
            <div class="row g-4 mb-4">
                <div class="col-lg-6">
                    <div class="card-clean">
                        <div class="section-title">Berita {{ $country->name ?? '-' }}</div>

                        @forelse ($newsItems as $item)
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

                                <div class="mt-2 text-muted" style="font-size: 12px;">
                                    Source: {{ $item->source ?? '-' }}
                                    |
                                    Positive: {{ $item->positive_score }}
                                    |
                                    Negative: {{ $item->negative_score }}
                                </div>
                            </div>
                        @empty
                            <p class="text-muted">Belum ada berita untuk negara ini.</p>
                        @endforelse
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card-clean">
                        <div class="section-title">Seluruh Sinyal Berita</div>

                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                <tr>
                                    <th>Negara</th>
                                    <th>Kategori</th>
                                    <th>Sentimen</th>
                                    <th>Skor</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($allNews as $item)
                                    <tr>
                                        <td><strong>{{ $item->country_name ?? '-' }}</strong></td>
                                        <td>{{ $item->category ?? 'Umum' }}</td>
                                        <td>
                                            <span class="risk-badge
                                                {{ $item->sentiment === 'Negative' ? 'risk-high' : ($item->sentiment === 'Positive' ? 'risk-low' : 'risk-medium') }}">
                                                {{ $item->sentiment }}
                                            </span>
                                        </td>
                                        <td>
                                            +{{ $item->positive_score }}
                                            /
                                            -{{ $item->negative_score }}
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
    const positivePercentage = Number(@json($sentimentSummary['positive_percentage']));
    const neutralPercentage = Number(@json($sentimentSummary['neutral_percentage']));
    const negativePercentage = Number(@json($sentimentSummary['negative_percentage']));

    new Chart(document.getElementById('sentimentChart'), {
        type: 'doughnut',
        data: {
            labels: ['Positif', 'Netral', 'Negatif'],
            datasets: [{
                data: [positivePercentage, neutralPercentage, negativePercentage],
                backgroundColor: ['#22c55e', '#f59e0b', '#ef4444'],
                borderWidth: 0
            }]
        },
        options: {
            cutout: '65%',
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