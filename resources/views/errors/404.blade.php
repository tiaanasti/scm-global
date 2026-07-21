<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Halaman Tidak Ditemukan | SCM Global</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background-color: #f8fafc;
            color: #0f172a;
            font-family: system-ui, -apple-system, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .error-card {
            max-width: 500px;
            width: 100%;
            padding: 2.5rem;
            background: #ffffff;
            border-radius: 1rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
            text-align: center;
        }
        .error-icon {
            font-size: 4rem;
            color: #f59e0b;
            margin-bottom: 1rem;
        }
        .error-code {
            font-size: 1.25rem;
            font-weight: 700;
            color: #f59e0b;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="error-icon">
            <i class="bi bi-compass"></i>
        </div>
        <div class="error-code mb-2">Error 404</div>
        <h1 class="h3 fw-bold mb-3">Halaman Tidak Ditemukan</h1>
        <p class="text-secondary mb-4">Halaman yang Anda cari tidak dapat ditemukan atau telah dipindahkan.</p>
        <a href="{{ url('/') }}" class="btn btn-primary px-4 py-2">
            <i class="bi bi-house-door me-2"></i>Kembali ke Dashboard
        </a>
    </div>
</body>
</html>
