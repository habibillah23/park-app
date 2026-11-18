<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Parkir')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-950 min-h-screen text-gray-200">

    <!-- Ambient glow -->
    <div class="pointer-events-none fixed -top-24 -left-24 h-72 w-72 rounded-full bg-blue-600/20 blur-3xl"></div>
    <div class="pointer-events-none fixed -bottom-24 -right-24 h-72 w-72 rounded-full bg-emerald-600/10 blur-3xl"></div>

    <header class="relative border-b border-slate-800 bg-slate-900/60 backdrop-blur">
        <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-6 py-4">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-500/10 ring-1 ring-blue-400/40 shadow-[0_0_20px_-4px_rgba(59,130,246,0.7)]">
                    <span class="text-lg font-bold text-blue-400">P</span>
                </span>
                <span class="font-bold text-gray-100">Sistem Parkir</span>
            </a>

            <nav class="flex flex-wrap items-center justify-end gap-1 text-sm">
                @auth
                    @if (auth()->user()->role === 'pelanggan')
                        <a href="{{ route('booking.create') }}" class="rounded-md px-3 py-1.5 text-gray-300 hover:bg-slate-800 hover:text-white">Buat Booking</a>
                        <a href="{{ route('booking.index') }}" class="rounded-md px-3 py-1.5 text-gray-300 hover:bg-slate-800 hover:text-white">Booking Saya</a>
                    @else
                        <a href="{{ route('dashboard') }}" class="rounded-md px-3 py-1.5 text-gray-300 hover:bg-slate-800 hover:text-white">Dashboard</a>
                    @endif
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="rounded-md px-3 py-1.5 text-gray-400 hover:bg-slate-800 hover:text-white">Keluar</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="rounded-md px-3 py-1.5 text-gray-300 hover:bg-slate-800 hover:text-white">Masuk</a>
                    <a href="{{ route('register') }}" class="rounded-md bg-blue-600 px-3 py-1.5 font-medium text-white shadow-[0_0_20px_-4px_rgba(59,130,246,0.8)] hover:bg-blue-500">Daftar</a>
                @endauth
            </nav>
        </div>
    </header>

    <main class="relative mx-auto max-w-6xl px-6 py-10">
        @if (session('success'))
            <div class="mb-6 rounded-md bg-emerald-500/10 px-4 py-3 text-sm text-emerald-400 ring-1 ring-emerald-500/30">
                {{ session('success') }}
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="relative border-t border-slate-800 py-6 text-center text-xs text-gray-500">
        Sistem Parkir &middot; Jam operasional <span class="text-amber-400">06:00 - 12:30</span>
    </footer>
</body>
</html>
