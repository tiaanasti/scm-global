<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <meta
        name="csrf-token"
        content="<?php echo e(csrf_token()); ?>"
    >

    <title>
        <?php echo $__env->yieldContent('title', 'Supply Chain Management'); ?>
    </title>

    
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        rel="stylesheet"
    >

    
    <link
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        rel="stylesheet"
    >

    
    <link
        rel="stylesheet"
        href="<?php echo e(asset('css/style.css')); ?>?v=<?php echo e(file_exists(public_path('css/style.css'))
                ? filemtime(public_path('css/style.css'))
                : time()); ?>"
    >

    

    <?php echo $__env->yieldPushContent('styles'); ?>
</head>

<body>
    <div class="app-shell">

        
        <aside
            class="sidebar sidebar-desktop"
            aria-label="Navigasi utama"
        >
            <?php echo $__env->make('partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </aside>

        
        <div
            class="offcanvas offcanvas-start scm-offcanvas"
            tabindex="-1"
            id="mobileSidebar"
            aria-labelledby="mobileSidebarLabel"
        >
            <div class="offcanvas-header">
                <h5
                    class="offcanvas-title text-white"
                    id="mobileSidebarLabel"
                >
                    Menu
                </h5>

                <button
                    type="button"
                    class="btn-close btn-close-white"
                    data-bs-dismiss="offcanvas"
                    aria-label="Tutup menu"
                ></button>
            </div>

            <div class="offcanvas-body">
                <div class="sidebar">
                    <?php echo $__env->make('partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>
            </div>
        </div>

        
        <main class="main" id="mainContent">

            
            <header class="mobile-header">
                <button
                    class="btn btn-outline-scm"
                    type="button"
                    data-bs-toggle="offcanvas"
                    data-bs-target="#mobileSidebar"
                    aria-controls="mobileSidebar"
                    aria-label="Buka menu"
                >
                    <i class="bi bi-list fs-5"></i>
                </button>

                <div class="mobile-brand">
                    Supply Chain Management
                </div>
            </header>

            
            <?php echo $__env->yieldContent('content'); ?>
        </main>
    </div>

    
    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    ></script>

    
    <script
        src="https://cdn.jsdelivr.net/npm/chart.js"
    ></script>

    
    <script
        src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    ></script>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html><?php /**PATH D:\laragon\www\supply-chain-management\resources\views/layouts/app.blade.php ENDPATH**/ ?>