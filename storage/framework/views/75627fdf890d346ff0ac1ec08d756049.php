<?php $__env->startSection('title', 'Daftar - Supply Chain Management'); ?>

<?php $__env->startSection('content'); ?>
    <div class="login-wrapper">
        <div class="login-card card-clean">
            <div class="login-brand">
                <div class="brand-icon">
                    <i class="bi bi-person-plus"></i>
                </div>
                <div>
                    <h1>Buat Akun Baru</h1>
                    <p>Daftar untuk memantau risiko rantai pasok dan mengelola watchlist pribadi.</p>
                </div>
            </div>

            <?php if($errors->any()): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0 ps-3">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="<?php echo e(route('register')); ?>" method="POST" class="login-form">
                <?php echo csrf_field(); ?>

                <div class="mb-3">
                    <label for="name" class="form-label">Nama Lengkap</label>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        class="form-control"
                        value="<?php echo e(old('name')); ?>"
                        required
                        autofocus
                        autocomplete="name"
                    >
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        class="form-control"
                        value="<?php echo e(old('email')); ?>"
                        required
                        autocomplete="username"
                    >
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Kata Sandi</label>
                    <input
                        type="password"
                        name="password"
                        id="password"
                        class="form-control"
                        required
                        autocomplete="new-password"
                    >
                    <div class="form-text">Minimal 8 karakter.</div>
                </div>

                <div class="mb-4">
                    <label for="password_confirmation" class="form-label">Konfirmasi Kata Sandi</label>
                    <input
                        type="password"
                        name="password_confirmation"
                        id="password_confirmation"
                        class="form-control"
                        required
                        autocomplete="new-password"
                    >
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-person-check"></i>
                    Daftar
                </button>
            </form>

            <div class="auth-switch mt-4">
                <small class="text-muted">
                    Sudah punya akun?
                    <a href="<?php echo e(route('login')); ?>">Masuk di sini</a>
                </small>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
    <?php echo $__env->make('auth.partials.guest-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laragon\www\supply-chain-management\resources\views\auth\register.blade.php ENDPATH**/ ?>