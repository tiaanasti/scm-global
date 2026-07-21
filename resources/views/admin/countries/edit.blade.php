@extends('layouts.app')

@section('title', 'Edit Negara - Supply Chain Management')

@section('content')
    <div class="topbar">
        <div class="page-title">
            <h1>Edit Negara</h1>
            <p>Perbarui data negara, indikator ekonomi, cuaca, kurs, dan skor risiko.</p>
        </div>

        <a href="{{ route('admin.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left"></i>
            Kembali ke Admin
        </a>
    </div>

    <div class="content">
        @if ($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('admin.countries.update', $country->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- DATA NEGARA -->
            <div class="card-clean mb-4">
                <div class="section-title">Data Negara</div>

                <div class="row g-3">
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label">Kode Negara</label>
                        <input type="text" name="country_code" class="form-control" value="{{ old('country_code', $country->country_code) }}" required>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <label class="form-label">Nama Negara</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $country->name) }}" required>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <label class="form-label">Ibu Kota</label>
                        <input type="text" name="capital" class="form-control" value="{{ old('capital', $country->capital) }}">
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <label class="form-label">Region</label>
                        <input type="text" name="region" class="form-control" value="{{ old('region', $country->region) }}">
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <label class="form-label">Kode Mata Uang</label>
                        <input type="text" name="currency_code" class="form-control" value="{{ old('currency_code', $country->currency_code) }}" required>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <label class="form-label">Nama Mata Uang</label>
                        <input type="text" name="currency_name" class="form-control" value="{{ old('currency_name', $country->currency_name) }}">
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <label class="form-label">Bahasa</label>
                        <input type="text" name="language" class="form-control" value="{{ old('language', $country->language) }}">
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <label class="form-label">Latitude</label>
                        <input type="number" step="0.0000001" name="latitude" class="form-control" value="{{ old('latitude', $country->latitude) }}">
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <label class="form-label">Longitude</label>
                        <input type="number" step="0.0000001" name="longitude" class="form-control" value="{{ old('longitude', $country->longitude) }}">
                    </div>
                </div>
            </div>

            <!-- DATA EKONOMI -->
            <div class="card-clean mb-4">
                <div class="section-title">Indikator Ekonomi</div>

                <div class="row g-3">
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label">GDP</label>
                        <input type="number" step="0.01" name="gdp" class="form-control" value="{{ old('gdp', $economic->gdp ?? 0) }}">
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <label class="form-label">Inflasi (%)</label>
                        <input type="number" step="0.01" name="inflation_rate" class="form-control" value="{{ old('inflation_rate', $economic->inflation_rate ?? 0) }}">
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <label class="form-label">Populasi</label>
                        <input type="number" name="population" class="form-control" value="{{ old('population', $economic->population ?? 0) }}">
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <label class="form-label">Ekspor</label>
                        <input type="number" step="0.01" name="exports" class="form-control" value="{{ old('exports', $economic->exports ?? 0) }}">
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <label class="form-label">Impor</label>
                        <input type="number" step="0.01" name="imports" class="form-control" value="{{ old('imports', $economic->imports ?? 0) }}">
                    </div>
                </div>
            </div>

            <!-- CUACA DAN KURS -->
            <div class="row g-4 mb-4">
                <div class="col-lg-6">
                    <div class="card-clean">
                        <div class="section-title">Cuaca</div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Temperatur</label>
                                <input type="number" step="0.01" name="temperature" class="form-control" value="{{ old('temperature', $weather->temperature ?? 0) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Status Cuaca</label>
                                <input type="text" name="weather_status" class="form-control" value="{{ old('weather_status', $weather->weather_status ?? 'Berawan') }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card-clean">
                        <div class="section-title">Kurs</div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Exchange Rate</label>
                                <input type="number" step="0.000001" name="exchange_rate" class="form-control" value="{{ old('exchange_rate', $currency->exchange_rate ?? 1) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Perubahan (%)</label>
                                <input type="number" step="0.01" name="change_percentage" class="form-control" value="{{ old('change_percentage', $currency->change_percentage ?? 0) }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RISK SCORE -->
            <div class="card-clean mb-4">
                <div class="section-title">Skor Risiko</div>

                <div class="row g-3">
                    <div class="col-lg-2 col-md-4">
                        <label class="form-label">Cuaca</label>
                        <input type="number" min="0" max="100" name="weather_score" class="form-control" value="{{ old('weather_score', $risk->weather_score ?? 0) }}">
                    </div>

                    <div class="col-lg-2 col-md-4">
                        <label class="form-label">Inflasi</label>
                        <input type="number" min="0" max="100" name="inflation_score" class="form-control" value="{{ old('inflation_score', $risk->inflation_score ?? 0) }}">
                    </div>

                    <div class="col-lg-2 col-md-4">
                        <label class="form-label">Kurs</label>
                        <input type="number" min="0" max="100" name="currency_score" class="form-control" value="{{ old('currency_score', $risk->currency_score ?? 0) }}">
                    </div>

                    <div class="col-lg-2 col-md-4">
                        <label class="form-label">Berita</label>
                        <input type="number" min="0" max="100" name="news_score" class="form-control" value="{{ old('news_score', $risk->news_score ?? 0) }}">
                    </div>

                    <div class="col-lg-2 col-md-4">
                        <label class="form-label">Total</label>
                        <input type="number" min="0" max="100" name="total_score" class="form-control" value="{{ old('total_score', $risk->total_score ?? 0) }}">
                    </div>

                    <div class="col-lg-2 col-md-4">
                        <label class="form-label">Level Risiko</label>
                        <select name="risk_level" class="form-select">
                            <option value="Risiko Rendah" {{ old('risk_level', $risk->risk_level ?? '') === 'Risiko Rendah' ? 'selected' : '' }}>Risiko Rendah</option>
                            <option value="Risiko Sedang" {{ old('risk_level', $risk->risk_level ?? '') === 'Risiko Sedang' ? 'selected' : '' }}>Risiko Sedang</option>
                            <option value="Risiko Tinggi" {{ old('risk_level', $risk->risk_level ?? '') === 'Risiko Tinggi' ? 'selected' : '' }}>Risiko Tinggi</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Rekomendasi</label>
                        <textarea name="recommendation" rows="4" class="form-control">{{ old('recommendation', $risk->recommendation ?? '') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 mb-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i>
                    Simpan Perubahan
                </button>

                <a href="{{ route('admin.index') }}" class="btn btn-outline-secondary">
                    Batal
                </a>
            </div>
        </form>

        <div class="footer">
            © {{ date('Y') }} Supply Chain Management. Semua hak dilindungi.
        </div>
    </div>
@endsection