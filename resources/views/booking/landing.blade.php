@extends('booking.layout')

@section('title', 'Booking Parkir - Sistem Parkir')

@section('content')
    <!-- Hero -->
    <section class="max-w-2xl">
        <h1 class="text-3xl font-bold leading-tight text-gray-100 sm:text-4xl">
            Pesan tempat parkir tanpa antre.
        </h1>
        <p class="mt-3 text-sm leading-relaxed text-gray-400">
            Pilih area, tentukan jam kedatangan, dan dapatkan kode booking. Tunjukkan kode itu ke petugas saat tiba.
        </p>

        @if (! auth()->check() || auth()->user()->role === 'pelanggan')
            <div class="mt-6 flex flex-wrap items-center gap-3">
                <a href="{{ route('booking.create') }}"
                   class="rounded-md bg-blue-600 px-5 py-2.5 text-sm font-medium text-white shadow-[0_0_20px_-4px_rgba(59,130,246,0.8)] transition-colors hover:bg-blue-500">
                    Booking Sekarang
                </a>
                @guest
                    <span class="text-xs text-gray-500">Anda perlu masuk atau mendaftar terlebih dahulu.</span>
                @endguest
            </div>
        @endif
    </section>

    <!-- Area parkir -->
    <section class="mt-12">
        <h2 class="text-lg font-semibold text-gray-100">Area parkir</h2>
        <p class="mt-1 text-xs text-gray-500">Sisa slot untuk satu jam ke depan.</p>

        @if ($areas->isEmpty())
            <p class="mt-4 text-sm text-gray-400">Belum ada area parkir yang aktif.</p>
        @else
            <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($areas as $area)
                    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-slate-900 to-slate-950 p-5 ring-1 ring-slate-700/50">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h3 class="font-semibold text-gray-100">{{ $area->nama_area }}</h3>
                                @if ($area->lokasi)
                                    <p class="mt-0.5 text-xs text-gray-500">{{ $area->lokasi }}</p>
                                @endif
                            </div>
                            @if ($area->sisa_slot > 0)
                                <span class="rounded-full bg-emerald-500/10 px-2.5 py-1 text-xs font-medium text-emerald-400 ring-1 ring-emerald-500/30">Tersedia</span>
                            @else
                                <span class="rounded-full bg-red-500/10 px-2.5 py-1 text-xs font-medium text-red-400 ring-1 ring-red-500/30">Penuh</span>
                            @endif
                        </div>
                        <p class="mt-4 text-sm text-gray-400">
                            <span class="text-2xl font-bold text-gray-100">{{ $area->sisa_slot }}</span>
                            dari {{ $area->kapasitas }} slot
                        </p>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    <!-- Tarif -->
    <section class="mt-12">
        <h2 class="text-lg font-semibold text-gray-100">Tarif parkir</h2>

        @if ($tarifs->isEmpty())
            <p class="mt-4 text-sm text-gray-400">Tarif belum diatur.</p>
        @else
            <div class="mt-4 overflow-x-auto rounded-2xl ring-1 ring-slate-700/50">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-900 text-xs uppercase tracking-wide text-gray-400">
                        <tr>
                            <th class="px-4 py-3">Jenis kendaraan</th>
                            <th class="px-4 py-3">Jam pertama</th>
                            <th class="px-4 py-3">Jam berikutnya</th>
                            <th class="px-4 py-3">Maksimal harian</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 bg-slate-900/40">
                        @foreach ($tarifs as $tarif)
                            <tr>
                                <td class="px-4 py-3 font-medium capitalize text-gray-100">{{ $tarif->jenis_kendaraan }}</td>
                                <td class="px-4 py-3">Rp {{ number_format($tarif->tarif_jam_pertama, 0, ',', '.') }}</td>
                                <td class="px-4 py-3">Rp {{ number_format($tarif->tarif_jam_berikutnya, 0, ',', '.') }}</td>
                                <td class="px-4 py-3">
                                    {{ $tarif->tarif_maksimal_harian ? 'Rp ' . number_format($tarif->tarif_maksimal_harian, 0, ',', '.') : '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

    <!-- Aturan singkat -->
    <section class="mt-12 max-w-2xl">
        <h2 class="text-lg font-semibold text-gray-100">Aturan booking</h2>
        <ul class="mt-3 list-disc space-y-1 pl-5 text-sm leading-relaxed text-gray-400">
            <li>Jam operasional <span class="text-amber-400">{{ $jamBuka }} - {{ $jamTutup }}</span>. Booking harus berada di dalam jam ini.</li>
            <li>Booking yang tidak datang lebih dari 30 menit dari jam mulai otomatis hangus dan slotnya dilepas.</li>
            <li>Biaya akhir dihitung dari durasi parkir sebenarnya saat kendaraan keluar.</li>
        </ul>
    </section>
@endsection
