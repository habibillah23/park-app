<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah 'pelanggan' ke enum role yang sudah ada, dan jadikan default
        // supaya akun yang daftar sendiri tidak otomatis jadi petugas.
        DB::statement(
            "ALTER TABLE users MODIFY role ENUM('admin','petugas','owner','pelanggan') NOT NULL DEFAULT 'pelanggan'"
        );
    }

    public function down(): void
    {
        // Pelanggan dikembalikan jadi petugas sebelum enum dipersempit
        DB::table('users')->where('role', 'pelanggan')->update(['role' => 'petugas']);

        DB::statement(
            "ALTER TABLE users MODIFY role ENUM('admin','petugas','owner') NOT NULL DEFAULT 'petugas'"
        );
    }
};
