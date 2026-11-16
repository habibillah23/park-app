<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Parkir</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-950 min-h-screen">

    <div class="grid min-h-screen lg:grid-cols-2">

        <!-- ============ KIRI: FORM LOGIN ============ -->
        <div class="relative flex items-center justify-center overflow-hidden px-6 py-10">

            <!-- Ambient glow -->
            <div class="absolute -top-24 -left-24 h-72 w-72 rounded-full bg-blue-600/20 blur-3xl"></div>
            <div class="absolute -bottom-24 -right-24 h-72 w-72 rounded-full bg-emerald-600/10 blur-3xl"></div>

            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-slate-900 to-slate-950 shadow-[0_0_40px_-8px_rgba(59,130,246,0.35)] ring-1 ring-slate-700/50 w-full max-w-sm p-8">
                <div class="absolute -right-10 -top-10 h-32 w-32 rounded-full bg-blue-500/10 blur-2xl"></div>

                <div class="relative text-center mb-8">
                    <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-xl bg-blue-500/10 ring-1 ring-blue-400/40 shadow-[0_0_20px_-4px_rgba(59,130,246,0.7)]">
                        <span class="text-2xl font-bold text-blue-400 drop-shadow-[0_0_8px_rgba(96,165,250,0.8)]">P</span>
                    </div>
                    <h1 class="text-xl font-bold text-gray-100">Sistem Parkir</h1>
                    <p class="text-sm text-gray-400 mt-1">Silakan login untuk melanjutkan</p>
                </div>

                @if ($errors->any())
                    <div class="relative mb-4 rounded-md bg-red-500/10 text-red-400 ring-1 ring-red-500/30 px-4 py-3 text-sm">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST" class="relative space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus
                               class="w-full bg-slate-800/60 border border-slate-700 text-gray-100 placeholder-gray-500 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Password</label>
                        <input type="password" name="password" required
                               class="w-full bg-slate-800/60 border border-slate-700 text-gray-100 placeholder-gray-500 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50">
                    </div>
                    <label class="flex items-center gap-2 text-sm text-gray-400">
                        <input type="checkbox" name="remember" class="rounded border-slate-600 bg-slate-800 text-blue-500 focus:ring-blue-500/50">
                        Ingat saya
                    </label>
                    <button type="submit"
                            class="w-full bg-blue-600 hover:bg-blue-500 text-white font-medium py-2 rounded-md text-sm shadow-[0_0_20px_-4px_rgba(59,130,246,0.8)] transition-colors">
                        Login
                    </button>
                </form>

                <p class="relative text-center text-sm text-gray-400 mt-6">
                    Belum punya akun?
                    <a href="{{ route('register') }}" class="text-blue-400 hover:text-blue-300 hover:underline font-medium">Daftar</a>
                </p>

                <p class="relative text-center text-xs text-gray-500 mt-4">
                    Dengan masuk atau mendaftar, Anda menyetujui aturan penggunaan sistem.
                </p>
                <button type="button" id="openHelp"
                        class="relative mx-auto mt-2 block rounded px-2 py-1 text-sm font-medium text-blue-400 underline underline-offset-4 hover:text-blue-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/50">
                    Cara penggunaan &amp; aturan parkir
                </button>
            </div>
        </div>

        <!-- ============ KANAN: INFO AREA PARKIR (disembunyikan di HP) ============ -->
        <aside class="relative hidden lg:flex flex-col justify-center overflow-hidden border-l border-slate-800 bg-gradient-to-br from-blue-950 via-slate-900 to-slate-950 px-14 py-10">

            <div class="absolute -right-20 top-10 h-72 w-72 rounded-full bg-blue-500/10 blur-3xl"></div>

            <div class="relative max-w-md">
                <h2 class="text-3xl font-bold leading-tight text-gray-100">
                    Kelola parkir lebih rapi dan cepat.
                </h2>
                <p class="mt-3 text-sm leading-relaxed text-gray-400">
                    Sistem Parkir membantu petugas dan admin mencatat kendaraan, menghitung biaya, dan memantau area parkir dari satu tempat.
                </p>

                <dl class="mt-8 space-y-5 border-l border-blue-400/30 pl-5">
                    <div>
                        <dt class="text-sm font-semibold text-gray-100">Area parkir</dt>
                        <dd class="mt-0.5 text-sm leading-relaxed text-gray-400">Atur area dan kapasitas, lalu lihat kendaraan yang sedang parkir.</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-semibold text-gray-100">Tarif per jam</dt>
                        <dd class="mt-0.5 text-sm leading-relaxed text-gray-400">Biaya dihitung otomatis dari durasi parkir dan jenis kendaraan.</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-semibold text-gray-100">Data kendaraan</dt>
                        <dd class="mt-0.5 text-sm leading-relaxed text-gray-400">Catat plat nomor, jenis kendaraan, serta waktu masuk dan keluar.</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-semibold text-gray-100">Log aktivitas</dt>
                        <dd class="mt-0.5 text-sm leading-relaxed text-gray-400">Setiap aktivitas tercatat dan tidak dapat dihapus oleh petugas.</dd>
                    </div>
                </dl>

                <p class="mt-8 text-sm text-gray-300">
                    Jam operasional: <span class="text-amber-400">06:00 - 12:30</span>
                </p>
            </div>
        </aside>
    </div>

    <!-- ============ MODAL BANTUAN ============ -->
    <dialog id="helpModal" aria-labelledby="helpTitle"
            class="m-auto w-[calc(100%-2rem)] max-w-lg rounded-2xl bg-slate-900 p-0 text-gray-200 ring-1 ring-blue-400/25 shadow-2xl backdrop:bg-slate-950/70">
        <div class="flex max-h-[85vh] flex-col">
            <div class="flex items-center justify-between gap-3 border-b border-slate-700/60 px-5 py-4">
                <h2 id="helpTitle" class="text-base font-semibold text-gray-100">Cara penggunaan &amp; aturan parkir</h2>
                <button type="button" id="closeHelp" aria-label="Tutup"
                        class="flex h-8 w-8 items-center justify-center rounded-lg text-xl leading-none text-gray-400 hover:bg-slate-800 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/50">
                    &times;
                </button>
            </div>

            <div class="overflow-y-auto px-5 pb-5 pt-1 text-sm leading-relaxed text-gray-300">
                <h3 class="mb-1.5 mt-4 font-semibold text-gray-100">Cara kerja sistem</h3>
                <ol class="list-decimal space-y-1 pl-5">
                    <li>Login dengan akun yang sudah terdaftar.</li>
                    <li>Petugas mencatat kendaraan yang masuk: plat nomor, jenis kendaraan, dan area parkir.</li>
                    <li>Sistem menghitung durasi dan biaya berdasarkan tarif yang berlaku.</li>
                    <li>Saat kendaraan keluar, petugas mengonfirmasi pembayaran dan data tersimpan di log aktivitas.</li>
                </ol>

                <h3 class="mb-1.5 mt-4 font-semibold text-gray-100">Hak akses</h3>
                <ul class="list-disc space-y-1 pl-5">
                    <li><strong>Admin</strong>: mengelola user, tarif, area parkir, dan melihat log aktivitas.</li>
                    <li><strong>Petugas</strong>: mencatat kendaraan masuk dan keluar.</li>
                </ul>

                <h3 class="mb-1.5 mt-4 font-semibold text-gray-100">Aturan parkir</h3>
                <ul class="list-disc space-y-1 pl-5">
                    <li>Jam operasional: <span class="text-amber-400">06:00 - 12:30</span>.</li>
                    <li>Tarif dihitung per jam dan berbeda tiap jenis kendaraan.</li>
                    <li>Karcis atau kode parkir yang hilang dikenakan denda: <span class="text-amber-400">RP:10,000</span>.</li>
                    <li>Setiap aktivitas tercatat dan tidak dapat dihapus oleh petugas.</li>
                </ul>

                <h3 class="mb-1.5 mt-4 font-semibold text-gray-100">Butuh bantuan?</h3>
                <p>Jika lupa password atau akun bermasalah, hubungi admin: <span class="text-amber-400">admin@gmail.parkir</span>.</p>
            </div>
        </div>
    </dialog>

    <script>
        (function () {
            const modal = document.getElementById('helpModal');
            document.getElementById('openHelp').addEventListener('click', () => modal.showModal());
            document.getElementById('closeHelp').addEventListener('click', () => modal.close());
            // klik area gelap di luar kotak modal untuk menutup
            modal.addEventListener('click', (e) => {
                if (e.target === modal) modal.close();
            });
        })();
    </script>
</body>
</html>