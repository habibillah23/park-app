<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
use App\Models\AreaParkir;
use App\Models\Booking;
use App\Models\TarifParkir;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class BookingController extends Controller
{
    private const JAM_BUKA = '06:00';
    private const JAM_TUTUP = '12:30';
    private const TOLERANSI_MENIT = 30;   // booking hangus jika telat lebih dari ini
    private const MAKS_HARI_KE_DEPAN = 7;
    private const MAKS_DURASI_JAM = 6;

    /**
     * Halaman publik: info area, sisa slot, tarif, jam operasional.
     */
    public function landing(): View
    {
        $this->hangusKadaluarsa();

        $sekarang = now();

        $areas = AreaParkir::where('status', 'aktif')->get()->map(function ($area) use ($sekarang) {
            $area->sisa_slot = $this->sisaSlot($area, $sekarang, $sekarang->copy()->addHour());

            return $area;
        });

        return view('booking.landing', [
            'areas' => $areas,
            'tarifs' => TarifParkir::all(),
            'jamBuka' => self::JAM_BUKA,
            'jamTutup' => self::JAM_TUTUP,
        ]);
    }

    /**
     * Riwayat booking milik pelanggan yang sedang login.
     */
    public function index(): View
    {
        $this->hangusKadaluarsa();

        $bookings = Booking::with('area')
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('booking.index', compact('bookings'));
    }

    public function create(): View
    {
        $this->hangusKadaluarsa();

        return view('booking.create', [
            'areas' => AreaParkir::where('status', 'aktif')->get(),
            'tarifs' => TarifParkir::all(),
            'jamBuka' => self::JAM_BUKA,
            'jamTutup' => self::JAM_TUTUP,
            'maksDurasi' => self::MAKS_DURASI_JAM,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'area_id' => ['required', 'exists:area_parkirs,id'],
            'jenis_kendaraan' => ['required', 'in:motor,mobil,truk,bus'],
            'plat_nomor' => ['required', 'string', 'max:20'],
            'waktu_mulai' => ['required', 'date'],
            'durasi_jam' => ['required', 'integer', 'min:1', 'max:' . self::MAKS_DURASI_JAM],
        ]);

        $mulai = Carbon::parse($data['waktu_mulai']);
        $selesai = $mulai->copy()->addHours((int) $data['durasi_jam']);
        $plat = strtoupper(trim($data['plat_nomor']));

        if ($mulai->lt(now()->startOfMinute())) {
            $this->tolak('waktu_mulai', 'Waktu mulai tidak boleh di masa lalu.');
        }

        if ($mulai->gt(now()->addDays(self::MAKS_HARI_KE_DEPAN))) {
            $this->tolak('waktu_mulai', 'Booking maksimal ' . self::MAKS_HARI_KE_DEPAN . ' hari ke depan.');
        }

        $buka = $mulai->copy()->setTimeFromTimeString(self::JAM_BUKA);
        $tutup = $mulai->copy()->setTimeFromTimeString(self::JAM_TUTUP);

        if ($mulai->lt($buka) || $selesai->gt($tutup)) {
          $this->tolak('waktu_mulai', 'Booking Anda berakhir pukul ' . $selesai->format('H:i') . ', di luar jam operasional (' . self::JAM_BUKA . ' - ' . self::JAM_TUTUP . ').');
        }

        $tarif = TarifParkir::where('jenis_kendaraan', $data['jenis_kendaraan'])->first();

        if (! $tarif) {
            $this->tolak('jenis_kendaraan', 'Tarif untuk jenis kendaraan ini belum diatur.');
        }

        // Hitungan biaya memakai method yang sama dengan proses kendaraan keluar
        $estimasi = (int) $tarif->hitungBiaya((int) $data['durasi_jam'] * 60);

        $booking = DB::transaction(function () use ($data, $mulai, $selesai, $plat, $estimasi) {
            // Kunci baris area agar dua booking bersamaan tidak sama-sama lolos
            $area = AreaParkir::whereKey($data['area_id'])->lockForUpdate()->firstOrFail();

            if ($area->status !== 'aktif') {
                $this->tolak('area_id', 'Area parkir ini sedang tidak aktif.');
            }

            if ($this->sisaSlot($area, $mulai, $selesai) < 1) {
                $this->tolak('area_id', 'Slot area ini penuh pada waktu tersebut. Coba area atau jam lain.');
            }

            $bentrok = Booking::where('plat_nomor', $plat)
                ->where('status', Booking::STATUS_DIPESAN)
                ->where('waktu_mulai', '<', $selesai)
                ->where('waktu_selesai', '>', $mulai)
                ->exists();

            if ($bentrok) {
                $this->tolak('plat_nomor', 'Plat nomor ini sudah punya booking di rentang waktu tersebut.');
            }

            return Booking::create([
                'user_id' => Auth::id(),
                'area_id' => $area->id,
                'jenis_kendaraan' => $data['jenis_kendaraan'],
                'plat_nomor' => $plat,
                'waktu_mulai' => $mulai,
                'waktu_selesai' => $selesai,
                'estimasi_biaya' => $estimasi,
                'status' => Booking::STATUS_DIPESAN,
            ]);
        });

        ActivityLogger::log("Booking dibuat: {$booking->kode_booking} ({$plat})");

        return redirect()->route('booking.tiket', $booking)
    ->with('success', "Booking berhasil. Kode booking Anda: {$booking->kode_booking}");
    }
    public function tiket(Booking $booking): View
    {
        abort_unless((int) $booking->user_id === (int) Auth::id(), 403);

        $booking->load('area');

        return view('booking.tiket', compact('booking'));
    }
    public function batal(Booking $booking): RedirectResponse
    {
        abort_unless((int) $booking->user_id === (int) Auth::id(), 403);
        abort_if($booking->status !== Booking::STATUS_DIPESAN, 400, 'Booking ini tidak bisa dibatalkan.');

        $booking->update(['status' => Booking::STATUS_BATAL]);

        ActivityLogger::log("Booking dibatalkan: {$booking->kode_booking}");

        return back()->with('success', 'Booking berhasil dibatalkan.');
    }

    /**
     * Sisa slot = kapasitas - kendaraan yang sedang parkir - booking aktif
     * yang bertabrakan dengan rentang waktu. Booking yang sudah check-in
     * berstatus 'parkir', jadi tidak terhitung ganda dengan transaksi.
     * Hitungan ini sengaja konservatif.
     */
    private function sisaSlot(AreaParkir $area, Carbon $mulai, Carbon $selesai): int
    {
        $sedangParkir = Transaksi::where('area_parkir_id', $area->id)
            ->where('status', 'masuk')
            ->count();

        $dipesan = Booking::where('area_id', $area->id)
            ->where('status', Booking::STATUS_DIPESAN)
            ->where('waktu_mulai', '<', $selesai)
            ->where('waktu_selesai', '>', $mulai)
            ->count();

        return max(0, $area->kapasitas - $sedangParkir - $dipesan);
    }

    /**
     * Booking yang tidak datang lewat toleransi dianggap hangus,
     * sehingga slotnya terlepas lagi.
     */
    private function hangusKadaluarsa(): void
    {
        Booking::where('status', Booking::STATUS_DIPESAN)
            ->where('waktu_mulai', '<', now()->subMinutes(self::TOLERANSI_MENIT))
            ->update(['status' => Booking::STATUS_HANGUS]);
    }

    private function tolak(string $field, string $pesan): never
    {
        throw ValidationException::withMessages([$field => $pesan]);
    }
}
