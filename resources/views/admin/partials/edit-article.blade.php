<form action="{{ route('admin.articles.update', $article->id) }}" method="POST" class="data-ajax-edit-form" data-refresh-section="articles-section">
    @csrf
    @method('PUT')

    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Judul Artikel</label>
            <input type="text" name="title" class="form-control" value="{{ old('title', $article->title) }}" required>
        </div>

        <div class="col-md-3">
            <label class="form-label">Kategori</label>
            <input type="text" name="category" class="form-control" value="{{ old('category', $article->category) }}">
        </div>

        <div class="col-md-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select" required>
                <option value="Draft" {{ old('status', $article->status) === 'Draft' ? 'selected' : '' }}>Draft</option>
                <option value="Published" {{ old('status', $article->status) === 'Published' ? 'selected' : '' }}>Published</option>
            </select>
        </div>

        <div class="col-12">
            <label class="form-label">Isi Artikel</label>
            <textarea name="content" rows="8" class="form-control" required>{{ old('content', $article->content) }}</textarea>
        </div>
    </div>

    <div class="mt-4 d-flex justify-content-end gap-2">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-save me-1"></i> Simpan Perubahan
        </button>
    </div>
</form>
