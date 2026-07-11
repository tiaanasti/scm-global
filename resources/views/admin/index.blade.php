<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Panel Admin - Supply Chain Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap & Icons -->
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
                <h1>Panel Admin</h1>
                <p>Kelola ringkasan data sistem, user, negara, pelabuhan, artikel, API log, dan kamus sentimen.</p>
            </div>
        </div>

        <div class="content">
            @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger">
        {{ $errors->first() }}
    </div>
@endif
<div class="card-clean mb-4">
    <div class="section-title">Tambah Negara Baru</div>

    <form action="{{ route('admin.countries.store') }}" method="POST">
        @csrf

        <div class="row g-3">
            <div class="col-lg-3 col-md-6">
                <label class="form-label">Kode Negara</label>
                <input type="text" name="country_code" class="form-control" placeholder="JP" required>
                <div class="metric-sub mt-1">Contoh: JP, SG, TH</div>
            </div>

            <div class="col-lg-3 col-md-6">
                <label class="form-label">Nama Negara</label>
                <input type="text" name="name" class="form-control" placeholder="Japan" required>
            </div>

            <div class="col-lg-3 col-md-6">
                <label class="form-label">Ibu Kota</label>
                <input type="text" name="capital" class="form-control" placeholder="Tokyo">
            </div>

            <div class="col-lg-3 col-md-6">
                <label class="form-label">Region</label>
                <input type="text" name="region" class="form-control" placeholder="Asia Timur">
            </div>

            <div class="col-lg-3 col-md-6">
                <label class="form-label">Kode Mata Uang</label>
                <input type="text" name="currency_code" class="form-control" placeholder="JPY" required>
            </div>

            <div class="col-lg-3 col-md-6">
                <label class="form-label">Nama Mata Uang</label>
                <input type="text" name="currency_name" class="form-control" placeholder="Yen">
            </div>

            <div class="col-lg-3 col-md-6">
                <label class="form-label">Bahasa</label>
                <input type="text" name="language" class="form-control" placeholder="Japanese">
            </div>

            <div class="col-lg-3 col-md-6">
                <label class="form-label">Exchange Rate</label>
                <input type="number" step="0.000001" name="exchange_rate" class="form-control" placeholder="157.35">
            </div>

            <div class="col-lg-3 col-md-6">
                <label class="form-label">Latitude</label>
                <input type="number" step="0.0000001" name="latitude" class="form-control" placeholder="35.6762">
            </div>

            <div class="col-lg-3 col-md-6">
                <label class="form-label">Longitude</label>
                <input type="number" step="0.0000001" name="longitude" class="form-control" placeholder="139.6503">
            </div>

            <div class="col-lg-6 col-md-12 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-plus-circle"></i>
                    Tambah Negara ke Sistem
                </button>
            </div>
        </div>
    </form>

    <div class="recommendation-box mt-4">
        <strong>Catatan:</strong>
        <div class="mt-2">
            Setelah negara ditambahkan, sistem otomatis membuat data awal untuk ekonomi, cuaca, kurs, risiko, pelabuhan, dan berita agar negara langsung bisa muncul di dashboard dan watchlist.
        </div>
    </div>
</div>
            <!-- SUMMARY -->
            <div class="row g-3 mb-4">
                <div class="col-lg-3 col-md-6">
                    <div class="card-clean metric-card">
                        <div class="metric-icon icon-blue">
                            <i class="bi bi-people"></i>
                        </div>
                        <div>
                            <div class="metric-label">User</div>
                            <div class="metric-value">{{ $summary['users_count'] }}</div>
                            <div class="metric-sub">Pengguna sistem</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card-clean metric-card">
                        <div class="metric-icon icon-green">
                            <i class="bi bi-globe2"></i>
                        </div>
                        <div>
                            <div class="metric-label">Negara</div>
                            <div class="metric-value">{{ $summary['countries_count'] }}</div>
                            <div class="metric-sub">Data negara</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card-clean metric-card">
                        <div class="metric-icon icon-orange">
                            <i class="bi bi-pin-map-fill"></i>
                        </div>
                        <div>
                            <div class="metric-label">Pelabuhan</div>
                            <div class="metric-value">{{ $summary['ports_count'] }}</div>
                            <div class="metric-sub">Dataset pelabuhan</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card-clean metric-card">
                        <div class="metric-icon icon-red">
                            <i class="bi bi-newspaper"></i>
                        </div>
                        <div>
                            <div class="metric-label">Berita</div>
                            <div class="metric-value">{{ $summary['news_count'] }}</div>
                            <div class="metric-sub">News cache</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-lg-3 col-md-6">
                    <div class="card-clean metric-card">
                        <div class="metric-icon icon-purple">
                            <i class="bi bi-file-text"></i>
                        </div>
                        <div>
                            <div class="metric-label">Artikel</div>
                            <div class="metric-value">{{ $summary['articles_count'] }}</div>
                            <div class="metric-sub">Artikel analisis</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card-clean metric-card">
                        <div class="metric-icon icon-blue">
                            <i class="bi bi-star"></i>
                        </div>
                        <div>
                            <div class="metric-label">Watchlist</div>
                            <div class="metric-value">{{ $summary['watchlists_count'] }}</div>
                            <div class="metric-sub">Negara dipantau</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card-clean metric-card">
                        <div class="metric-icon icon-green">
                            <i class="bi bi-plus-circle"></i>
                        </div>
                        <div>
                            <div class="metric-label">Positive Words</div>
                            <div class="metric-value">{{ $summary['positive_words_count'] }}</div>
                            <div class="metric-sub">Kamus positif</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card-clean metric-card">
                        <div class="metric-icon icon-red">
                            <i class="bi bi-dash-circle"></i>
                        </div>
                        <div>
                            <div class="metric-label">Negative Words</div>
                            <div class="metric-value">{{ $summary['negative_words_count'] }}</div>
                            <div class="metric-sub">Kamus negatif</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- USERS + API LOGS -->
            <div class="row g-4 mb-4">
                <div class="col-lg-6">
                    <div class="card-clean">
                        <div class="section-title">Data User</div>

                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Dibuat</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($users as $user)
                                    <tr>
                                        <td><strong>{{ $user->name }}</strong></td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ \Carbon\Carbon::parse($user->created_at)->format('d M Y') }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card-clean">
                        <div class="section-title">API Logs</div>

                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                <tr>
                                    <th>API</th>
                                    <th>Status</th>
                                    <th>Waktu</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse ($apiLogs as $log)
                                    <tr>
                                        <td>
                                            <strong>{{ $log->api_name }}</strong>
                                            <div class="metric-sub">{{ $log->endpoint ?? '-' }}</div>
                                        </td>
                                        <td>
                                            <span class="risk-badge risk-low">{{ $log->status }}</span>
                                        </td>
                                        <td>
                                            {{ $log->requested_at ? \Carbon\Carbon::parse($log->requested_at)->format('d M Y H:i') : '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-muted">Belum ada log API.</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- COUNTRIES TABLE -->
            <div class="card-clean mb-4">
                <div class="section-title">Dataset Negara dan Risiko</div>

                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                        <tr>
                            <th>Negara</th>
                            <th>Wilayah</th>
                            <th>Mata Uang</th>
                            <th>Skor Risiko</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($countries as $country)
                            <tr>
                                <td><strong>{{ $country->name }}</strong></td>
                                <td>{{ $country->region ?? '-' }}</td>
                                <td>{{ $country->currency_code ?? '-' }}</td>
                                <td>{{ $country->total_score ?? 0 }}/100</td>
                                <td>
                                    <span class="risk-badge
                                        {{ ($country->total_score ?? 0) >= 60 ? 'risk-high' : (($country->total_score ?? 0) >= 35 ? 'risk-medium' : 'risk-low') }}">
                                        {{ $country->risk_level ?? '-' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- PORTS + ARTICLES -->
            <div class="row g-4 mb-4">
                <div class="col-lg-7">
                    <div class="card-clean">
                        <div class="section-title">Dataset Pelabuhan</div>

                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                <tr>
                                    <th>Pelabuhan</th>
                                    <th>Negara</th>
                                    <th>Status</th>
                                    <th>Risiko</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($ports as $port)
                                    <tr>
                                        <td>
                                            <i class="bi bi-pin-map-fill text-primary"></i>
                                            <strong>{{ $port->name }}</strong>
                                            <div class="metric-sub">{{ $port->city ?? '-' }}</div>
                                        </td>
                                        <td>{{ $port->country_name ?? '-' }}</td>
                                        <td>
                                            <span class="risk-badge
                                                {{ $port->status === 'Aman' ? 'risk-low' : ($port->status === 'Waspada' ? 'risk-high' : 'risk-medium') }}">
                                                {{ $port->status }}
                                            </span>
                                        </td>
                                        <td>{{ $port->port_risk_score }}/100</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="card-clean">
                        <div class="section-title">Artikel Analisis</div>

                        @forelse ($articles as $article)
                            <div class="news-item">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="risk-badge {{ $article->status === 'Published' ? 'risk-low' : 'risk-medium' }}">
                                        {{ $article->status }}
                                    </span>
                                    <small class="text-muted">{{ $article->category ?? '-' }}</small>
                                </div>

                                <div class="news-title">{{ $article->title }}</div>
                                <div class="news-desc">
                                    Penulis: {{ $article->author_name ?? 'Admin' }}
                                </div>
                            </div>
                        @empty
                            <p class="text-muted">Belum ada artikel.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- SENTIMENT WORDS -->
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="card-clean">
                        <div class="section-title">Kamus Kata Positif</div>

                        @foreach ($positiveWords as $word)
                            <span class="risk-badge risk-low me-1 mb-2">
                                {{ $word->word }}
                            </span>
                        @endforeach
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card-clean">
                        <div class="section-title">Kamus Kata Negatif</div>

                        @foreach ($negativeWords as $word)
                            <span class="risk-badge risk-high me-1 mb-2">
                                {{ $word->word }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="footer">
                © {{ date('Y') }} Supply Chain Management. Semua hak dilindungi.
            </div>
        </div>
    </main>
</div>
</body>
</html>