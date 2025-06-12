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
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            // Foreign key ke tabel raw_materials
            // onDelete('cascade') berarti jika raw_material dihapus, entri stoknya juga ikut terhapus
            $table->foreignId('raw_material_id')->constrained('raw_materials')->onDelete('cascade');
            $table->integer('ready_stock')->default(0); // Jumlah stok yang siap digunakan
            $table->integer('in_process_stock')->default(0); // Jumlah stok yang masih dalam proses
            $table->string('process_status')->nullable(); // Keterangan status proses (misal: 'Dalam pengiriman', 'Menunggu QC')
            $table->boolean('is_critical')->default(false); // Kolom untuk menandai apakah stok ini kritis (akan diisi oleh sistem)
            $table->timestamps();

            // Memastikan setiap raw_material hanya memiliki satu entri stok utama
            $table->unique('raw_material_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};