<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Smart Home IoT Monitor')</title>

    <!-- Tailwind CSS (CDN for quick setup) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Firebase JS SDK (compat for realtime DB) -->
    <script src="https://www.gstatic.com/firebasejs/10.7.1/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/10.7.1/firebase-database-compat.js"></script>

    <style>
        /* small global tweaks to match previous design intent */
        body {
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
            background: linear-gradient(135deg, #0f172a 0%, #111827 60%);
        }

        .glass {
            background: rgba(255, 255, 255, 0.04);
            backdrop-filter: blur(8px);
            border-radius: 1rem;
            border: 1px solid rgba(255, 255, 255, 0.06);
        }
    </style>
</head>

<body class="min-h-screen text-white">
    <!-- Navbar -->
    <nav class="w-full bg-transparent py-4">
        <div class="max-w-7xl mx-auto px-4 flex items-center justify-between">
            <a href="/" class="flex items-center gap-3">
                <i class="fas fa-home text-2xl text-indigo-400"></i>
                <span class="text-xl font-semibold text-white">Smart Home IoT</span>
            </a>
            <div>
                <a href="{{ route('monitor') }}" class="text-white/90 hover:text-white px-3 py-2 rounded-md">Monitor</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 py-6">
        @yield('content')
    </main>

    <!-- Load separated page JS (deferred) -->
    <script src="{{ asset('js/monitor.js') }}" defer></script>

    @yield('scripts')
</body>

</html>