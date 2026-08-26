<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pj_kades', function (Blueprint $table) {
            $table->enum('metode', ['online', 'offline'])->default('online')->after('status');
            $table->string('berkas_zip')->nullable()->after('metode');
            $table->text('catatan_admin')->nullable()->after('berkas_zip');
            $table->string('posisi_surat')->nullable()->default('Pegawai')->after('catatan_admin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pj_kades', function (Blueprint $table) {
            $table->dropColumn(['metode', 'berkas_zip', 'catatan_admin', 'posisi_surat']);
        });
    }
};
