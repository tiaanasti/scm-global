@extends('layouts.app')

@section('title', 'Daftar Pantauan - Supply Chain Management')

@section('content')
    <div class="topbar">
        <div class="page-title">
            <h1>Daftar Pantauan</h1>
            <p>Negara yang dipantau untuk monitoring risiko rantai pasok secara berkelanjutan.</p>
        </div>
    </div>

    <div class="content">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="card-clean mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <div class="section-title mb-1">Tambah Negara ke Watchlist</div>
                    <div class="metric-sub">
                        Pilih negara yang ingin dipantau secara berkala.
                    </div>
                </div>
            </div>

            <form action="{{ route('watchlists.store') }}" method="POST" class="row g-3">
                @csrf

                <div class="col-lg-9">
                    <select name="country_id" class="form-select country-select" required>
                        <option value="">Pilih negara</option>

                        @foreach ($availableCountries as $country)
                            <option value="{{ $country->id }}">
                                {{ $country->name }} - {{ $country->region }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-plus-circle"></i>
                        Tambah Watchlist
                    </button>
                </div>
            </form>

            @if ($availableCountries->count() === 0)
                <div class="metric-sub mt-3">
                    Semua negara sudah masuk ke dalam watchlist.
                </div>
            @endif
        </div>

        <div class="row g-3 mb-4">
            <div class="col-lg-3 col-md-6">
                <div class="card-clean metric-card">
                    <div class="metric-icon icon-blue">
                        <i class="bi bi-star"></i>
                    </div>
                    <div>
                        <div class="metric-label">Total Watchlist</div>
                        <div class="metric-value">{{ $summary['total_watchlist'] }}</div>
                        <div class="metric-sub">Negara dipantau</div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card-clean metric-card">
                    <div class="metric-icon icon-red">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                    <div>
                        <div class="metric-label">Risiko Tinggi</div>
                        <div class="metric-value">{{ $summary['high_risk'] }}</div>
                        <div class="metric-sub">Butuh perhatian utama</div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card-clean metric-card">
                    <div class="metric-icon icon-orange">
                        <i class="bi bi-dash-circle"></i>
                    </div>
                    <div>
                        <div class="metric-label">Risiko Sedang</div>
                        <div class="metric-value">{{ $summary['medium_risk'] }}</div>
                        <div class="metric-sub">Perlu dipantau</div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card-clean metric-card">
                    <div class="metric-icon icon-green">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div>
                        <div class="metric-label">Risiko Rendah</div>
                        <div class="metric-value">{{ $summary['low_risk'] }}</div>
                        <div class="metric-sub">Relatif aman</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-clean mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="section-title mb-0">Negara dalam Daftar Pantauan</div>
                <span class="risk-badge risk-medium">Monitoring aktif</span>
            </div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                    <tr>
                        <th>Negara</th>
                        <th>Wilayah</th>
                        <th>Kurs</th>
                        <th>Cuaca</th>
                        <th>Skor Risiko</th>
                        <th>Status</th>
                        <th>Mulai Dipantau</th>
                        <th>Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($watchlistRows as $item)
                        <tr>
                            <td>
                                <strong>{{ $item->country_name }}</strong>
                                <div class="metric-sub">{{ $item->currency_code }}</div>
                            </td>
                            <td>{{ $item->region ?? '-' }}</td>
                            <td>
                                USD/{{ $item->target_currency ?? '-' }}
                                <div class="metric-sub">
                                    {{ number_format($item->exchange_rate ?? 0, 2) }}
                                </div>
                            </td>
                            <td>
                                {{ $item->temperature ?? 0 }}°C
                                <div class="metric-sub">{{ $item->weather_status ?? '-' }}</div>
                            </td>
                            <td>
                                <strong>{{ $item->total_score ?? 0 }}/100</strong>
                            </td>
                            <td>
                                <span class="risk-badge
                                    {{ ($item->total_score ?? 0) >= 60 ? 'risk-high' : (($item->total_score ?? 0) >= 35 ? 'risk-medium' : 'risk-low') }}">
                                    {{ $item->risk_level ?? '-' }}
                                </span>
                            </td>
                            <td>
                                {{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}
                            </td>
                            <td>
                                <form
                                    action="{{ route('watchlists.destroy', $item->watchlist_id) }}"
                                    method="POST"
                                    onsubmit="return confirm('Hapus negara ini dari watchlist?')"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-muted">
                                Belum ada negara dalam daftar pantauan.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row g-4">
            @foreach ($watchlistRows as $item)
                <div class="col-lg-6">
                    <div class="card-clean">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <div class="section-title mb-1">{{ $item->country_name }}</div>
                                <div class="metric-sub">{{ $item->region ?? '-' }}</div>
                            </div>

                            <span class="risk-badge
                                {{ ($item->total_score ?? 0) >= 60 ? 'risk-high' : (($item->total_score ?? 0) >= 35 ? 'risk-medium' : 'risk-low') }}">
                                {{ $item->risk_level ?? '-' }}
                            </span>
                        </div>

                        <div class="recommendation-box">
                            <strong>Catatan Pemantauan:</strong>
                            <div class="mt-2">
                                {{ $item->recommendation ?? 'Belum ada rekomendasi untuk negara ini.' }}
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="footer">
            © {{ date('Y') }} Supply Chain Management. Semua hak dilindungi.
        </div>
    </div>
@endsection
