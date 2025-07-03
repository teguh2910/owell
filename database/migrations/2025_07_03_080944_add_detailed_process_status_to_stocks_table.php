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
            // Kolom status proses yang lama (jika ada) bisa dihapus atau di-rename menjadi summary
            // Untuk saat ini, kita akan tambahkan kolom baru
            $table->string('kansai_process_status')->nullable()->after('process_status');
            $table->string('owell_process_status')->nullable()->after('kansai_process_status');
            $table->string('qa_aiia_process_status')->nullable()->after('owell_process_status');

            // Opsional: Hapus kolom process_status lama jika tidak lagi diperlukan
            $table->dropColumn('process_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->dropColumn('kansai_process_status');
            $table->dropColumn('owell_process_status');
            $table->dropColumn('qa_aiia_process_status');
            // Opsional: Tambahkan kembali kolom process_status lama jika dihapus di up()
            $table->string('process_status')->nullable();
        });
    }
};