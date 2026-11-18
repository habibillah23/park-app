<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Booking extends Model
{
    public const STATUS_DIPESAN = 'dipesan';
    public const STATUS_PARKIR  = 'parkir';
    public const STATUS_SELESAI = 'selesai';
    public const STATUS_BATAL   = 'batal';
    public const STATUS_HANGUS  = 'hangus';

    protected $fillable = [
        'kode_booking',
        'user_id',
        'area_id',
        'jenis_kendaraan',
        'plat_nomor',
        'waktu_mulai',
        'waktu_selesai',
        'estimasi_biaya',
        'status',
    ];

    protected $casts = [
        'waktu_mulai'   => 'datetime',
        'waktu_selesai' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Booking $booking) {
           if (empty($booking->kode_booking)) {
    do {
        $kode = 'BK-' . strtoupper(Str::random(6));
    } while (static::where('kode_booking', $kode)->exists());

    $booking->kode_booking = $kode;
}
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // SESUAIKAN: ganti AreaParkir dengan nama model area parkir kamu
    public function area(): BelongsTo
    {
        return $this->belongsTo(AreaParkir::class, 'area_id');
    }
}
