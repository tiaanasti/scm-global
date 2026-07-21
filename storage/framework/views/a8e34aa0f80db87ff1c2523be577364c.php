<?php $__env->startSection('title', 'Edit User - Supply Chain Management'); ?>

<?php $__env->startSection('content'); ?>
    <div class="topbar">
        <div class="page-title">
            <h1>Edit User</h1>
            <p>Perbarui nama, email, role, dan reset password pengguna.</p>
        </div>

        <a href="<?php echo e(route('admin.index')); ?>" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left"></i>
            Kembali ke Admin
        </a>
    </div>

    <div class="content">
        <?php if(session('error')): ?>
            <div class="alert alert-danger">
                <?php echo e(session('error')); ?>

            </div>
        <?php endif; ?>

        <?php if($errors->any()): ?>
            <div class="alert alert-danger">
                <?php echo e($errors->first()); ?>

            </div>
        <?php endif; ?>

        <form action="<?php echo e(route('admin.users.update', $user->id)); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div class="card-clean mb-4">
                <div class="section-title">Data User</div>

                <div class="row g-3">
                    <div class="col-lg-4 col-md-6">
                        <label class="form-label">Nama</label>
                        <input
                            type="text"
                            name="name"
                            class="form-control"
                            value="<?php echo e(old('name', $user->name)); ?>"
                            required
                        >
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <label class="form-label">Email</label>
                        <input
                            type="email"
                            name="email"
                            class="form-control"
                            value="<?php echo e(old('email', $user->email)); ?>"
                            required
                        >
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select" required>
                            <option value="user" <?php echo e(old('role', $user->role ?? 'user') === 'user' ? 'selected' : ''); ?>>
                                User
                            </option>
                            <option value="admin" <?php echo e(old('role', $user->role ?? 'user') === 'admin' ? 'selected' : ''); ?>>
                                Admin
                            </option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="card-clean mb-4">
                <div class="section-title">Reset Password</div>
                <div class="metric-sub mb-3">
                    Kosongkan jika tidak ingin mengubah password. Minimal 8 karakter jika diisi.
                </div>

                <div class="row g-3">
                    <div class="col-lg-4 col-md-6">
                        <label class="form-label">Password Baru</label>
                        <input type="password" name="password" class="form-control" autocomplete="new-password">
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <label class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" class="form-control" autocomplete="new-password">
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laragon\www\supply-chain-management\resources\views\admin\users\edit.blade.php ENDPATH**/ ?>