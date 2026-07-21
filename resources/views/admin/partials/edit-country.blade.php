<form action="{{ route('admin.countries.update', $country->id) }}" method="POST" class="data-ajax-edit-form" data-refresh-section="countries-section">
    @csrf
    @method('PUT')

    <h6>Data Negara</h6>
    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <label class="form-label">Kode Negara</label>
            <input type="text" name="country_code" class="form-control" value="{{ old('country_code', $country->country_code) }}" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Nama Negara</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $country->name) }}" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Ibu Kota</label>
            <input type="text" name="capital" class="form-control" value="{{ old('capital', $country->capital) }}">
        </div>
        <div class="col-md-4">
            <label class="form-label">Region</label>
            <input type="text" name="region" class="form-control" value="{{ old('region', $country->region) }}">
        </div>
        <div class="col-md-4">
            <label class="form-label">Kode Mata Uang</label>
            <input type="text" name="currency_code" class="form-control" value="{{ old('currency_code', $country->currency_code) }}" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Nama Mata Uang</label>
            <input type="text" name="currency_name" class="form-control" value="{{ old('currency_name', $country->currency_name) }}">
        </div>
        <div class="col-md-4">
            <label class="form-label">Bahasa</label>
            <input type="text" name="language" class="form-control" value="{{ old('language', $country->language) }}">
        </div>
        <div class="col-md-4">
            <label class="form-label">Latitude</label>
            <input type="number" step="0.0000001" name="latitude" class="form-control" value="{{ old('latitude', $country->latitude) }}">
        </div>
        <div class="col-md-4">
            <label class="form-label">Longitude</label>
            <input type="number" step="0.0000001" name="longitude" class="form-control" value="{{ old('longitude', $country->longitude) }}">
        </div>
    </div>

    <hr>
    <h6>Indikator Ekonomi</h6>
    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <label class="form-label">GDP</label>
            <input type="number" step="0.01" name="gdp" class="form-control" value="{{ old('gdp', $economic->gdp ?? 0) }}">
        </div>
        <div class="col-md-4">
            <label class="form-label">Inflasi (%)</label>
            <input type="number" step="0.01" name="inflation_rate" class="form-control" value="{{ old('inflation_rate', $economic->inflation_rate ?? 0) }}">
        </div>
        <div class="col-md-4">
            <label class="form-label">Populasi</label>
            <input type="number" name="population" class="form-control" value="{{ old('population', $economic->population ?? 0) }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Ekspor</label>
            <input type="number" step="0.01" name="exports" class="form-control" value="{{ old('exports', $economic->exports ?? 0) }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Impor</label>
            <input type="number" step="0.01" name="imports" class="form-control" value="{{ old('imports', $economic->imports ?? 0) }}">
        </div>
    </div>

    <hr>
    <h6>Cuaca & Kurs</h6>
    <div class="row g-3 mb-3">
        <div class="col-md-3">
            <label class="form-label">Temperatur</label>
            <input type="number" step="0.01" name="temperature" class="form-control" value="{{ old('temperature', $weather->temperature ?? 0) }}">
        </div>
        <div class="col-md-3">
            <label class="form-label">Status Cuaca</label>
            <input type="text" name="weather_status" class="form-control" value="{{ old('weather_status', $weather->weather_status ?? 'Berawan') }}">
        </div>
        <div class="col-md-3">
            <label class="form-label">Exchange Rate</label>
            <input type="number" step="0.000001" name="exchange_rate" class="form-control" value="{{ old('exchange_rate', $currency->exchange_rate ?? 1) }}">
        </div>
        <div class="col-md-3">
            <label class="form-label">Perubahan (%)</label>
            <input type="number" step="0.01" name="change_percentage" class="form-control" value="{{ old('change_percentage', $currency->change_percentage ?? 0) }}">
        </div>
    </div>

    <hr>
    <h6>Skor Risiko</h6>
    <div class="row g-3 mb-3">
        <div class="col-md-2">
            <label class="form-label">Cuaca</label>
            <input type="number" min="0" max="100" name="weather_score" class="form-control" value="{{ old('weather_score', $risk->weather_score ?? 0) }}">
        </div>
        <div class="col-md-2">
            <label class="form-label">Inflasi</label>
            <input type="number" min="0" max="100" name="inflation_score" class="form-control" value="{{ old('inflation_score', $risk->inflation_score ?? 0) }}">
        </div>
        <div class="col-md-2">
            <label class="form-label">Kurs</label>
            <input type="number" min="0" max="100" name="currency_score" class="form-control" value="{{ old('currency_score', $risk->currency_score ?? 0) }}">
        </div>
        <div class="col-md-2">
            <label class="form-label">Berita</label>
            <input type="number" min="0" max="100" name="news_score" class="form-control" value="{{ old('news_score', $risk->news_score ?? 0) }}">
        </div>
        <div class="col-md-2">
            <label class="form-label">Total</label>
            <input type="number" min="0" max="100" name="total_score" class="form-control" value="{{ old('total_score', $risk->total_score ?? 0) }}">
        </div>
        <div class="col-md-2">
            <label class="form-label">Level Risiko</label>
            <select name="risk_level" class="form-select">
                <option value="Risiko Rendah" {{ old('risk_level', $risk->risk_level ?? '') === 'Risiko Rendah' ? 'selected' : '' }}>Risiko Rendah</option>
                <option value="Risiko Sedang" {{ old('risk_level', $risk->risk_level ?? '') === 'Risiko Sedang' ? 'selected' : '' }}>Risiko Sedang</option>
                <option value="Risiko Tinggi" {{ old('risk_level', $risk->risk_level ?? '') === 'Risiko Tinggi' ? 'selected' : '' }}>Risiko Tinggi</option>
            </select>
        </div>
        <div class="col-12">
            <label class="form-label">Rekomendasi</label>
            <textarea name="recommendation" rows="3" class="form-control">{{ old('recommendation', $risk->recommendation ?? '') }}</textarea>
        </div>
    </div>

    <div class="mt-4 d-flex justify-content-end gap-2">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-save me-1"></i> Simpan Perubahan
        </button>
    </div>
</form>
