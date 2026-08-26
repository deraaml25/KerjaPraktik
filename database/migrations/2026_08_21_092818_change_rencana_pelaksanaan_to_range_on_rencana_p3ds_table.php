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
        Schema::table('rencana_p3ds', function (Blueprint $table) {
            $table->renameColumn('rencana_pelaksanaan', 'rencana_pelaksanaan_mulai');
            $table->date('rencana_pelaksanaan_selesai')->nullable()->after('rencana_pelaksanaan_mulai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rencana_p3ds', function (Blueprint $table) {
            $table->dropColumn('rencana_pelaksanaan_selesai');
            $table->renameColumn('rencana_pelaksanaan_mulai', 'rencana_pelaksanaan');
        });
    }
};
