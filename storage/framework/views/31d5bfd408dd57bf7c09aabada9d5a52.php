<div class="sidebar-inner">
    
    <a
        href="<?php echo e(route('dashboard')); ?>"
        class="brand"
        aria-label="Kembali ke Dashboard"
    >
        <span class="brand-icon" aria-hidden="true">
            <i class="bi bi-box-seam"></i>
        </span>

        <span class="brand-text">
            Supply Chain<br>
            Management
        </span>
    </a>

    
    <nav class="sidebar-nav" aria-label="Navigasi utama">
        <a
            href="<?php echo e(route('dashboard')); ?>"
            class="nav-link-custom <?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>"
            <?php if(request()->routeIs('dashboard')): ?> aria-current="page" <?php endif; ?>
        >
            <i class="bi bi-house-door-fill" aria-hidden="true"></i>
            <span>Dashboard</span>
        </a>

        <a
            href="<?php echo e(route('countries.index')); ?>"
            class="nav-link-custom <?php echo e(request()->routeIs('countries.*') ? 'active' : ''); ?>"
            <?php if(request()->routeIs('countries.*')): ?> aria-current="page" <?php endif; ?>
        >
            <i class="bi bi-globe2" aria-hidden="true"></i>
            <span>Negara</span>
        </a>

        <a
            href="<?php echo e(route('weather.index')); ?>"
            class="nav-link-custom <?php echo e(request()->routeIs('weather.*') ? 'active' : ''); ?>"
            <?php if(request()->routeIs('weather.*')): ?> aria-current="page" <?php endif; ?>
        >
            <i class="bi bi-cloud-sun-fill" aria-hidden="true"></i>
            <span>Cuaca Global</span>
        </a>

        <a
            href="<?php echo e(route('risks.index')); ?>"
            class="nav-link-custom <?php echo e(request()->routeIs('risks.*') ? 'active' : ''); ?>"
            <?php if(request()->routeIs('risks.*')): ?> aria-current="page" <?php endif; ?>
        >
            <i class="bi bi-shield-check" aria-hidden="true"></i>
            <span>Risiko</span>
        </a>

        <a
            href="<?php echo e(route('ports.index')); ?>"
            class="nav-link-custom <?php echo e(request()->routeIs('ports.*') ? 'active' : ''); ?>"
            <?php if(request()->routeIs('ports.*')): ?> aria-current="page" <?php endif; ?>
        >
            <i class="bi bi-pin-map-fill" aria-hidden="true"></i>
            <span>Pelabuhan</span>
        </a>

        <a
            href="<?php echo e(route('currencies.index')); ?>"
            class="nav-link-custom <?php echo e(request()->routeIs('currencies.*') ? 'active' : ''); ?>"
            <?php if(request()->routeIs('currencies.*')): ?> aria-current="page" <?php endif; ?>
        >
            <i class="bi bi-currency-dollar" aria-hidden="true"></i>
            <span>Kurs</span>
        </a>

        <a
            href="<?php echo e(route('news.index')); ?>"
            class="nav-link-custom <?php echo e(request()->routeIs('news.*') ? 'active' : ''); ?>"
            <?php if(request()->routeIs('news.*')): ?> aria-current="page" <?php endif; ?>
        >
            <i class="bi bi-newspaper" aria-hidden="true"></i>
            <span>Berita</span>
        </a>

        <a
            href="<?php echo e(route('comparisons.index')); ?>"
            class="nav-link-custom <?php echo e(request()->routeIs('comparisons.*') ? 'active' : ''); ?>"
            <?php if(request()->routeIs('comparisons.*')): ?> aria-current="page" <?php endif; ?>
        >
            <i class="bi bi-bar-chart-line-fill" aria-hidden="true"></i>
            <span>Perbandingan</span>
        </a>

        <a
            href="<?php echo e(route('watchlists.index')); ?>"
            class="nav-link-custom <?php echo e(request()->routeIs('watchlists.*') ? 'active' : ''); ?>"
            <?php if(request()->routeIs('watchlists.*')): ?> aria-current="page" <?php endif; ?>
        >
            <i class="bi bi-star-fill" aria-hidden="true"></i>
            <span>Watchlist</span>
        </a>

        <a
            href="<?php echo e(route('reports.index')); ?>"
            class="nav-link-custom <?php echo e(request()->routeIs('reports.*') ? 'active' : ''); ?>"
            <?php if(request()->routeIs('reports.*')): ?> aria-current="page" <?php endif; ?>
        >
            <i class="bi bi-file-earmark-bar-graph-fill" aria-hidden="true"></i>
            <span>Laporan</span>
        </a>

        <?php if(auth()->guard()->check()): ?>
            <?php if((auth()->user()->role ?? 'user') === 'admin'): ?>
                <a
                    href="<?php echo e(route('admin.index')); ?>"
                    class="nav-link-custom <?php echo e(request()->routeIs('admin.*') ? 'active' : ''); ?>"
                    <?php if(request()->routeIs('admin.*')): ?> aria-current="page" <?php endif; ?>
                >
                    <i class="bi bi-person-gear" aria-hidden="true"></i>
                    <span>Admin</span>
                </a>
            <?php endif; ?>
        <?php endif; ?>
    </nav>

    
    <div class="sidebar-intel">
        <strong>Supply Chain Intelligence</strong>

        <span>
            Pantau risiko global dan dukung pengambilan keputusan.
        </span>
    </div>

    
    <?php if(auth()->guard()->check()): ?>
        <div class="sidebar-user">
            <div class="user-avatar" aria-hidden="true">
                <i class="bi bi-person-fill"></i>
            </div>

            <div class="sidebar-user-content">
                <div
                    class="sidebar-user-name"
                    title="<?php echo e(auth()->user()->name); ?>"
                >
                    <?php echo e(auth()->user()->name); ?>

                </div>

                <div
                    class="sidebar-user-email"
                    title="<?php echo e(auth()->user()->email); ?>"
                >
                    <?php echo e(auth()->user()->email); ?>

                </div>

                <div class="sidebar-user-role">
                    <?php echo e(ucfirst(auth()->user()->role ?? 'user')); ?>

                </div>

                <form
                    action="<?php echo e(route('logout')); ?>"
                    method="POST"
                    class="mt-2"
                >
                    <?php echo csrf_field(); ?>

                    <button
                        type="submit"
                        class="btn btn-sm btn-light w-100"
                    >
                        <i
                            class="bi bi-box-arrow-right me-1"
                            aria-hidden="true"
                        ></i>

                        Keluar
                    </button>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div><?php /**PATH D:\laragon\www\supply-chain-management\resources\views/partials/sidebar.blade.php ENDPATH**/ ?>