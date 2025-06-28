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
            // Tambahkan kolom aii_stock sebagai integer, default 0
            // Lokasinya setelah expired_date (atau sesuaikan jika ingin di tempat lain)
            $table->integer('aiia_stock')->default(0)->after('expired_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->dropColumn('aiia_stock');
        });
    }
};