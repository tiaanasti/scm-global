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
        <?php echo $__env->yieldContent('title', 'Masuk - Supply Chain Management'); ?>
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
        rel="stylesheet"
        href="<?php echo e(asset('css/style.css')); ?>?v=<?php echo e(file_exists(public_path('css/style.css'))
                ? filemtime(public_path('css/style.css'))
                : time()); ?>"
    >

    <?php echo $__env->yieldPushContent('styles'); ?>
</head>

<body class="login-page">
    <main class="min-vh-100">
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    
    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    ></script>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html><?php /**PATH D:\laragon\www\supply-chain-management\resources\views\layouts\guest.blade.php ENDPATH**/ ?>