<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Log aktivitas tidak boleh dihapus
        DB::unprepared('DROP TRIGGER IF EXISTS activity_logs_no_delete');
        DB::unprepared("
            CREATE TRIGGER activity_logs_no_delete
            BEFORE DELETE ON activity_logs
            FOR EACH ROW
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Log aktivitas tidak boleh dihapus'
        ");

        // Log aktivitas tidak boleh diubah
        DB::unprepared('DROP TRIGGER IF EXISTS activity_logs_no_update');
        DB::unprepared("
            CREATE TRIGGER activity_logs_no_update
            BEFORE UPDATE ON activity_logs
            FOR EACH ROW
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Log aktivitas tidak boleh diubah'
        ");

        // Saat waktu_keluar terisi, status otomatis menjadi selesai
        DB::unprepared('DROP TRIGGER IF EXISTS transaksis_set_selesai');
        DB::unprepared("
            CREATE TRIGGER transaksis_set_selesai
            BEFORE UPDATE ON transaksis
            FOR EACH ROW
            SET NEW.status = IF(NEW.waktu_keluar IS NOT NULL AND NEW.status = 'masuk', 'selesai', NEW.status)
        ");
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS activity_logs_no_delete');
        DB::unprepared('DROP TRIGGER IF EXISTS activity_logs_no_update');
        DB::unprepared('DROP TRIGGER IF EXISTS transaksis_set_selesai');
    }
};