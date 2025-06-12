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
        Schema::table('stocks', function (Blueprint $table) {
            // Kolom baru untuk menyimpan tanggal estimasi stok habis
            // Nullable karena mungkin tidak ada kebutuhan terdaftar atau stok terlalu banyak
            $table->date('estimated_depletion_date')->nullable()->after('is_critical');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->dropColumn('estimated_depletion_date');
        });
    }
};