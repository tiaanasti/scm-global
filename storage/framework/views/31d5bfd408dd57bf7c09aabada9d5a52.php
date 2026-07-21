<aside class="sidebar">
    <div class="brand">
        <div class="brand-icon">
            <i class="bi bi-box-seam"></i>
        </div>
        <div>
            Supply Chain<br>Management
        </div>
    </div>

    <a href="<?php echo e(route('dashboard')); ?>" class="nav-link-custom <?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>">
        <i class="bi bi-house-door-fill"></i>
        <span>Dashboard</span>
    </a>

    <a href="<?php echo e(route('countries.index')); ?>" class="nav-link-custom <?php echo e(request()->routeIs('countries.index') ? 'active' : ''); ?>">
        <i class="bi bi-globe2"></i>
        <span>Negara</span>
    </a>

    <a href="<?php echo e(route('risks.index')); ?>" class="nav-link-custom <?php echo e(request()->routeIs('risks.index') ? 'active' : ''); ?>">
        <i class="bi bi-shield-check"></i>
        <span>Risiko</span>
    </a>

    <a href="<?php echo e(route('ports.index')); ?>" class="nav-link-custom <?php echo e(request()->routeIs('ports.index') ? 'active' : ''); ?>">
        <i class="bi bi-pin-map-fill"></i>
        <span>Pelabuhan</span>
    </a>

    <a href="<?php echo e(route('currencies.index')); ?>" class="nav-link-custom <?php echo e(request()->routeIs('currencies.index') ? 'active' : ''); ?>">
        <i class="bi bi-currency-dollar"></i>
        <span>Kurs</span>
    </a>

    <a href="<?php echo e(route('news.index')); ?>" class="nav-link-custom <?php echo e(request()->routeIs('news.index') ? 'active' : ''); ?>">
        <i class="bi bi-newspaper"></i>
        <span>Berita</span>
    </a>

    <a href="<?php echo e(route('comparisons.index')); ?>" class="nav-link-custom <?php echo e(request()->routeIs('comparisons.index') ? 'active' : ''); ?>">
        <i class="bi bi-bar-chart-line"></i>
        <span>Perbandingan</span>
    </a>

    <a href="<?php echo e(route('watchlists.index')); ?>" class="nav-link-custom <?php echo e(request()->routeIs('watchlists.index') ? 'active' : ''); ?>">
        <i class="bi bi-star"></i>
        <span>Watchlist</span>
    </a>

    <a href="<?php echo e(route('reports.index')); ?>" class="nav-link-custom <?php echo e(request()->routeIs('reports.index') ? 'active' : ''); ?>">
        <i class="bi bi-file-earmark-bar-graph"></i>
        <span>Laporan</span>
    </a>

    <?php if(auth()->check() && auth()->user()->role === 'admin'): ?>
        <a href="<?php echo e(route('admin.index')); ?>" class="nav-link-custom <?php echo e(request()->routeIs('admin.*') ? 'active' : ''); ?>">
            <i class="bi bi-person-gear"></i>
            <span>Admin</span>
        </a>
    <?php endif; ?>

    <?php if(auth()->guard()->check()): ?>
        <div class="sidebar-user">
            <div class="user-avatar">
                <i class="bi bi-person-fill"></i>
            </div>
            <div class="flex-grow-1">
                <div style="font-weight: 700;"><?php echo e(auth()->user()->name); ?></div>
                <div style="font-size: 13px; color: #bfdbfe;"><?php echo e(auth()->user()->email); ?></div>
                <form action="<?php echo e(route('logout')); ?>" method="POST" class="mt-2">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn btn-sm btn-light w-100">
                        <i class="bi bi-box-arrow-right"></i>
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    <?php endif; ?>
</aside>
<?php /**PATH D:\laragon\www\supply-chain-management\resources\views/partials/sidebar.blade.php ENDPATH**/ ?>