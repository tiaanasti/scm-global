

<?php $__env->startSection('title', 'Edit Artikel - Supply Chain Management'); ?>

<?php $__env->startSection('content'); ?>
    <div class="topbar">
        <div class="page-title">
            <h1>Edit Artikel Analisis</h1>
            <p>Perbarui artikel analisis yang ditampilkan pada panel admin.</p>
        </div>

        <a href="<?php echo e(route('admin.index')); ?>" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left"></i>
            Kembali ke Admin
        </a>
    </div>

    <div class="content">
        <?php if($errors->any()): ?>
            <div class="alert alert-danger">
                <?php echo e($errors->first()); ?>

            </div>
        <?php endif; ?>

        <form action="<?php echo e(route('admin.articles.update', $article->id)); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div class="card-clean mb-4">
                <div class="section-title">Data Artikel</div>

                <div class="row g-3">
                    <div class="col-lg-6">
                        <label class="form-label">Judul Artikel</label>
                        <input type="text" name="title" class="form-control" value="<?php echo e(old('title', $article->title)); ?>" required>
                    </div>

                    <div class="col-lg-3">
                        <label class="form-label">Kategori</label>
                        <input type="text" name="category" class="form-control" value="<?php echo e(old('category', $article->category)); ?>">
                    </div>

                    <div class="col-lg-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="Draft" <?php echo e(old('status', $article->status) === 'Draft' ? 'selected' : ''); ?>>Draft</option>
                            <option value="Published" <?php echo e(old('status', $article->status) === 'Published' ? 'selected' : ''); ?>>Published</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Isi Artikel</label>
                        <textarea name="content" rows="10" class="form-control" required><?php echo e(old('content', $article->content)); ?></textarea>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 mb-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i>
                    Simpan Perubahan
                </button>

                <a href="<?php echo e(route('admin.index')); ?>" class="btn btn-outline-secondary">
                    Batal
                </a>
            </div>
        </form>

        <div class="footer">
            © <?php echo e(date('Y')); ?> Supply Chain Management. Semua hak dilindungi.
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laragon\www\supply-chain-management\resources\views\admin\articles\edit.blade.php ENDPATH**/ ?>