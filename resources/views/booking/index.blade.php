@extends('booking.layout')

@section('title', 'Booking Saya - Sistem Parkir')

@section('content')
    @php
        $badge = [
            'dipesan' => 'bg-blue-500/10 text-blue-400 ring-blue-500/30',
            'parkir'  => 'bg-emerald-500/10 text-emerald-400 ring-emerald-500/30',
            'selesai' => 'bg-slate-500/10 text-slate-300 ring-slate-500/30',
            'batal'   => 'bg-slate-500/10 text-slate-400 ring-slate-500/30',
            'hangus'  => 'bg-red-500/10 text-red-400 ring-red-500/30',
        ];
    @endphp

    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-2xl font-bold text-gray-100">Booking Saya</h1>
        <a href="{{ route('booking.create') }}"
           class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-[0_0_20px_-4px_rgba(59,130,246,0.8)] transition-colors hover:bg-blue-500">
            Buat Booking
        </a>
    </div>

    @if ($bookings->isEmpty())
        <div class="mt-8 rounded-2xl bg-slate-900/60 p-8 text-center ring-1 ring-slate-700/50">
            <p class="text-sm text-gray-400">Anda belum punya booking.</p>
        </div>
    @else
        <div class="mt-6 overflow-x-auto rounded-2xl ring-1 ring-slate-700/50">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-900 text-xs uppercase tracking-wide text-gray-400">
                    <tr>
                        <th class="px-4 py-3">Kode</th>
                        <th class="px-4 py-3">Area</th>
                        <th class="px-4 py-3">Kendaraan</th>
                        <th class="px-4 py-3">Waktu</th>
                        <th class="px-4 py-3">Estimasi biaya</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800 bg-slate-900/40">
                    @foreach ($bookings as $booking)
                        <tr>
                            <td class="px-4 py-3 font-mono font-semibold text-gray-100">{{ $booking->kode_booking }}</td>
                            <td class="px-4 py-3">{{ $booking->area->nama_area ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <span class="font-medium text-gray-100">{{ $booking->plat_nomor }}</span>
                                <span class="block text-xs capitalize text-gray-500">{{ $booking->jenis_kendaraan }}</span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                {{ $booking->waktu_mulai->translatedFormat('d M Y') }}
                                <span class="block text-xs text-gray-500">
                                    {{ $booking->waktu_mulai->format('H:i') }} - {{ $booking->waktu_selesai->format('H:i') }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">Rp {{ number_format($booking->estimasi_biaya, 0, ',', '.') }}</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full px-2.5 py-1 text-xs font-medium capitalize ring-1 {{ $badge[$booking->status] ?? $badge['batal'] }}">
                                    {{ $booking->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                @if ($booking->status === 'dipesan')
                                    <form action="{{ route('booking.batal', $booking) }}" method="POST"
                                          onsubmit="return confirm('Batalkan booking {{ $booking->kode_booking }}?')">
                                        @csrf
                                        <button type="submit" class="rounded-md px-3 py-1.5 text-xs font-medium text-red-400 hover:bg-red-500/10">
                                            Batalkan
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $bookings->links() }}
        </div>
    @endif

    <p class="mt-6 text-xs text-gray-500">
        Tunjukkan kode booking ke petugas saat tiba. Estimasi biaya dapat berbeda dari biaya akhir karena biaya akhir dihitung dari durasi parkir sebenarnya.
    </p>
@endsection
