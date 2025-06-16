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
            // Kolom baru untuk menyimpan tanggal kedaluwarsa
            // Nullable karena mungkin ada raw material yang tidak memiliki expired date
            $table->date('expired_date')->nullable()->after('estimated_depletion_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->dropColumn('expired_date');
        });
    }
};