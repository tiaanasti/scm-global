<form action="{{ route('admin.ports.update', $port->id) }}" method="POST" class="data-ajax-edit-form" data-refresh-section="ports-section">
    @csrf
    @method('PUT')

    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Nama Pelabuhan</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $port->name) }}" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">Kota</label>
            <input type="text" name="city" class="form-control" value="{{ old('city', $port->city) }}">
        </div>

        <div class="col-md-6">
            <label class="form-label">Negara</label>
            <select name="country_id" class="form-select" required>
                @foreach ($countries as $c)
                    <option value="{{ $c->id }}" {{ old('country_id', $port->country_id) == $c->id ? 'selected' : '' }}>
                        {{ $c->name }} ({{ $c->country_code }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label">Latitude</label>
            <input type="number" step="0.000001" name="latitude" class="form-control" value="{{ old('latitude', $port->latitude) }}" required>
        </div>

        <div class="col-md-3">
            <label class="form-label">Longitude</label>
            <input type="number" step="0.000001" name="longitude" class="form-control" value="{{ old('longitude', $port->longitude) }}" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">Status Pelabuhan</label>
            <select name="status" class="form-select" required>
                @foreach (['Aman', 'Normal', 'Waspada', 'Siaga', 'Darurat'] as $st)
                    <option value="{{ $st }}" {{ old('status', $port->status) === $st ? 'selected' : '' }}>
                        {{ $st }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label">Skor Risiko Pelabuhan (0-100)</label>
            <input type="number" step="0.1" min="0" max="100" name="port_risk_score" class="form-control" value="{{ old('port_risk_score', $port->port_risk_score) }}" required>
        </div>
    </div>

    <div class="mt-4 d-flex justify-content-end gap-2">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-save me-1"></i> Simpan Perubahan
        </button>
    </div>
</form>
