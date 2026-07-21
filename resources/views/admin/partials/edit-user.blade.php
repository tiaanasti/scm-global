<form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="data-ajax-edit-form" data-refresh-section="users-section">
    @csrf
    @method('PUT')

    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Nama</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">Role</label>
            <select name="role" class="form-select" required>
                <option value="user" {{ old('role', $user->role ?? 'user') === 'user' ? 'selected' : '' }}>User</option>
                <option value="admin" {{ old('role', $user->role ?? 'user') === 'admin' ? 'selected' : '' }}>Admin</option>
            </select>
        </div>
    </div>

    <hr class="my-4">
    <h6>Reset Password</h6>
    <p class="text-muted small">Kosongkan jika tidak ingin mengubah password. Minimal 8 karakter jika diisi.</p>

    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Password Baru</label>
            <input type="password" name="password" class="form-control" autocomplete="new-password">
        </div>

        <div class="col-md-6">
            <label class="form-label">Konfirmasi Password Baru</label>
            <input type="password" name="password_confirmation" class="form-control" autocomplete="new-password">
        </div>
    </div>

    <div class="mt-4 d-flex justify-content-end gap-2">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-save me-1"></i> Simpan Perubahan
        </button>
    </div>
</form>
