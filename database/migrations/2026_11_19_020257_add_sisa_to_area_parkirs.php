<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('area_parkirs', function (Blueprint $table) {
            $table->unsignedInteger('sisa')->default(0)->after('kapasitas');
        });

        // Isi awal: kapasitas dikurangi kendaraan yang masih parkir
        DB::unprepared("
            UPDATE area_parkirs a
            SET a.sisa = GREATEST(a.kapasitas - (
                SELECT COUNT(*) FROM transaksis t
                WHERE t.area_parkir_id = a.id AND t.status = 'masuk'
            ), 0)
        ");

        // Tolak kendaraan masuk kalau area penuh
        DB::unprepared('DROP TRIGGER IF EXISTS transaksis_cek_penuh');
        DB::unprepared("
            CREATE TRIGGER transaksis_cek_penuh
            BEFORE INSERT ON transaksis
            FOR EACH ROW
            BEGIN
                IF (SELECT sisa FROM area_parkirs WHERE id = NEW.area_parkir_id) <= 0 THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Area parkir penuh';
                END IF;
            END
        ");

        // Kendaraan masuk: sisa berkurang 1
        DB::unprepared('DROP TRIGGER IF EXISTS transaksis_kurangi_sisa');
        DB::unprepared("
            CREATE TRIGGER transaksis_kurangi_sisa
            AFTER INSERT ON transaksis
            FOR EACH ROW
            UPDATE area_parkirs SET sisa = sisa - 1 WHERE id = NEW.area_parkir_id
        ");

        // Kendaraan keluar (status berubah ke selesai): sisa bertambah 1
        DB::unprepared('DROP TRIGGER IF EXISTS transaksis_tambah_sisa');
        DB::unprepared("
            CREATE TRIGGER transaksis_tambah_sisa
            AFTER UPDATE ON transaksis
            FOR EACH ROW
            BEGIN
                IF OLD.status = 'masuk' AND NEW.status = 'selesai' THEN
                    UPDATE area_parkirs SET sisa = sisa + 1 WHERE id = NEW.area_parkir_id;
                END IF;
            END
        ");
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS transaksis_cek_penuh');
        DB::unprepared('DROP TRIGGER IF EXISTS transaksis_kurangi_sisa');
        DB::unprepared('DROP TRIGGER IF EXISTS transaksis_tambah_sisa');

        Schema::table('area_parkirs', function (Blueprint $table) {
            $table->dropColumn('sisa');
        });
    }
};