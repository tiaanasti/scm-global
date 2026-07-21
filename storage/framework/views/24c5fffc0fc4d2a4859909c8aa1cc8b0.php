<?php $__env->startSection('title', 'Masuk - Supply Chain Management'); ?>

<?php $__env->startSection('content'); ?>
    <div class="login-wrapper">
        <div class="login-card card-clean">
            <div class="login-brand">
                <div class="brand-icon">
                    <i class="bi bi-box-seam"></i>
                </div>
                <div>
                    <h1>Supply Chain Management</h1>
                    <p>Masuk untuk mengakses dashboard risiko rantai pasok global.</p>
                </div>
            </div>

            <?php if(session('success')): ?>
                <div class="alert alert-success">
                    <?php echo e(session('success')); ?>

                </div>
            <?php endif; ?>

            <?php if($errors->any()): ?>
                <div class="alert alert-danger">
                    <?php echo e($errors->first()); ?>

                </div>
            <?php endif; ?>

            <form action="<?php echo e(route('login')); ?>" method="POST" class="login-form">
                <?php echo csrf_field(); ?>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        class="form-control"
                        value="<?php echo e(old('email')); ?>"
                        required
                        autofocus
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
                        autocomplete="current-password"
                    >
                </div>

                <div class="form-check mb-4">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        name="remember"
                        id="remember"
                        value="1"
                        <?php echo e(old('remember') ? 'checked' : ''); ?>

                    >
                    <label class="form-check-label" for="remember">
                        Ingat saya
                    </label>
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-box-arrow-in-right"></i>
                    Masuk
                </button>
            </form>

            <div class="auth-switch mt-4">
                <small class="text-muted">
                    Belum punya akun?
                    <a href="<?php echo e(route('register')); ?>">Daftar sekarang</a>
                </small>
            </div>

            <div class="login-hint mt-3">
                <small class="text-muted">
                    Demo admin: <strong>admin@supplyrisk.test</strong> / <strong>password</strong>
                </small>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
    <?php echo $__env->make('auth.partials.guest-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laragon\www\supply-chain-management\resources\views\auth\login.blade.php ENDPATH**/ ?>