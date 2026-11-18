<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tiket Parkir {{ $booking->kode_booking }}</title>
    <style>
        body { margin:0; min-height:100vh; display:flex; align-items:center; justify-content:center;
               background:#020617; color:#f1f5f9; font-family:system-ui,sans-serif; padding:16px; box-sizing:border-box; }
        .card { width:100%; max-width:380px; padding:24px; border-radius:16px; text-align:center;
                background:#0b1220; border:1px solid #1e293b; }
        .muted { color:#94a3b8; font-size:14px; }
        .qr { background:#fff; padding:16px; border-radius:12px; display:inline-block; margin:12px 0; }
        .kode { letter-spacing:3px; margin:8px 0 4px; font-size:28px; }
        .ok { color:#34d399; } .bad { color:#f87171; }
        .row { display:flex; justify-content:space-between; font-size:14px; padding:6px 0; border-top:1px solid #1e293b; }
        a { color:#60a5fa; text-decoration:none; }
    </style>
</head>
<body>
<div class="card">
    <h2 style="margin:0 0 4px">Tiket Parkir</h2>

    @if (session('success'))
        <p class="ok" style="font-size:14px">{{ session('success') }}</p>
    @endif

    @if ($booking->status === \App\Models\Booking::STATUS_DIPESAN)
        <p class="muted">Tunjukkan ke petugas saat tiba</p>
        <div class="qr">
            {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(200)->generate($booking->kode_booking) !!}
        </div>
    @else
        <p class="bad">Tiket tidak berlaku (status: {{ $booking->status }})</p>
    @endif

    <div class="kode">{{ $booking->kode_booking }}</div>

    <div style="margin-top:16px;text-align:left">
        <div class="row"><span class="muted">Plat nomor</span><span>{{ $booking->plat_nomor }}</span></div>
        <div class="row"><span class="muted">Kendaraan</span><span>{{ ucfirst($booking->jenis_kendaraan) }}</span></div>
        <div class="row"><span class="muted">Area</span><span>{{ $booking->area->nama_area ?? $booking->area->nama ?? '-' }}</span></div>
        <div class="row"><span class="muted">Waktu</span>
            <span>{{ $booking->waktu_mulai->format('d M Y H:i') }} – {{ $booking->waktu_selesai->format('H:i') }}</span></div>
        <div class="row"><span class="muted">Estimasi biaya</span>
            <span>Rp {{ number_format($booking->estimasi_biaya, 0, ',', '.') }}</span></div>
        <div class="row"><span class="muted">Status</span><span>{{ ucfirst($booking->status) }}</span></div>
    </div>

    <p style="margin-top:16px"><a href="{{ route('booking.index') }}">← Riwayat booking</a></p>
</div>
</body>
</html>