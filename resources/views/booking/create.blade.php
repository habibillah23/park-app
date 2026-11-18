@extends('booking.layout')

@section('title', 'Buat Booking - Sistem Parkir')

@section('content')
    @php
        $inputClass = 'w-full bg-slate-800/60 border border-slate-700 text-gray-100 placeholder-gray-500 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50';
    @endphp

    <div class="grid gap-8 lg:grid-cols-5">

        <!-- Form -->
        <div class="lg:col-span-3">
            <h1 class="text-2xl font-bold text-gray-100">Buat Booking</h1>
            <p class="mt-1 text-sm text-gray-400">Jam operasional {{ $jamBuka }} - {{ $jamTutup }}.</p>

            @if ($errors->any())
                <div class="mt-5 rounded-md bg-red-500/10 px-4 py-3 text-sm text-red-400 ring-1 ring-red-500/30">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('booking.store') }}" method="POST"
                  class="relative mt-5 space-y-4 rounded-2xl bg-gradient-to-br from-slate-900 to-slate-950 p-6 ring-1 ring-slate-700/50">
                @csrf

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-300">Area parkir</label>
                    <select name="area_id" required class="{{ $inputClass }}">
                        <option value="">Pilih area</option>
                        @foreach ($areas as $area)
                            <option value="{{ $area->id }}" @selected(old('area_id') == $area->id)>
                                {{ $area->nama_area }}@if ($area->lokasi) - {{ $area->lokasi }}@endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-300">Jenis kendaraan</label>
                        <select name="jenis_kendaraan" required class="{{ $inputClass }}">
                            <option value="">Pilih jenis</option>
                            @foreach (['motor', 'mobil', 'truk', 'bus'] as $jenis)
                                <option value="{{ $jenis }}" @selected(old('jenis_kendaraan') === $jenis)>{{ ucfirst($jenis) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-300">Plat nomor</label>
                        <input type="text" name="plat_nomor" value="{{ old('plat_nomor') }}" maxlength="20" required
                               placeholder="AB 1234 CD" class="{{ $inputClass }} uppercase">
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-300">Waktu kedatangan</label>
                        <input type="datetime-local" name="waktu_mulai" value="{{ old('waktu_mulai') }}"
                               min="{{ now()->format('Y-m-d\TH:i') }}"
                               max="{{ now()->addDays(7)->format('Y-m-d\TH:i') }}"
                               required class="{{ $inputClass }}">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-300">Lama parkir</label>
                        <select name="durasi_jam" required class="{{ $inputClass }}">
                            @for ($i = 1; $i <= $maksDurasi; $i++)
                                <option value="{{ $i }}" @selected((int) old('durasi_jam', 1) === $i)>{{ $i }} jam</option>
                            @endfor
                        </select>
                    </div>
                </div>

                <button type="submit"
                        class="w-full rounded-md bg-blue-600 py-2 text-sm font-medium text-white shadow-[0_0_20px_-4px_rgba(59,130,246,0.8)] transition-colors hover:bg-blue-500">
                    Pesan Sekarang
                </button>

                <p class="text-xs text-gray-500">
                    Estimasi biaya akan ditampilkan setelah booking dibuat. Booking yang tidak datang lebih dari 30 menit otomatis hangus.
                </p>
            </form>
        </div>

        <!-- Tarif -->
        <aside class="lg:col-span-2">
            <h2 class="text-lg font-semibold text-gray-100">Tarif</h2>
            <div class="mt-3 space-y-3">
                @forelse ($tarifs as $tarif)
                    <div class="rounded-xl bg-slate-900/60 p-4 ring-1 ring-slate-700/50">
                        <p class="font-medium capitalize text-gray-100">{{ $tarif->jenis_kendaraan }}</p>
                        <p class="mt-1 text-sm text-gray-400">
                            Jam pertama Rp {{ number_format($tarif->tarif_jam_pertama, 0, ',', '.') }},
                            berikutnya Rp {{ number_format($tarif->tarif_jam_berikutnya, 0, ',', '.') }}/jam
                            @if ($tarif->tarif_maksimal_harian)
                                , maksimal Rp {{ number_format($tarif->tarif_maksimal_harian, 0, ',', '.') }}/hari
                            @endif
                        </p>
                    </div>
                @empty
                    <p class="text-sm text-gray-400">Tarif belum diatur.</p>
                @endforelse
            </div>
        </aside>
    </div>
@endsection
