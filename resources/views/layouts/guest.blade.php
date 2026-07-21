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
        content="{{ csrf_token() }}"
    >

    <title>
        @yield('title', 'Masuk - Supply Chain Risk Intelligence')
    </title>

    {{-- Bootstrap 5 --}}
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    {{-- Bootstrap Icons --}}
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        rel="stylesheet"
    >

    {{-- CSS utama --}}
    <link
        rel="stylesheet"
        href="{{ asset('css/style.css') }}?v={{
            file_exists(public_path('css/style.css'))
                ? filemtime(public_path('css/style.css'))
                : time()
        }}"
    >

    @stack('styles')
</head>

<body class="login-page">
    <main class="min-vh-100">
        @yield('content')
    </main>

    {{-- Bootstrap 5 JavaScript --}}
    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    ></script>

    @stack('scripts')
</body>
</html>