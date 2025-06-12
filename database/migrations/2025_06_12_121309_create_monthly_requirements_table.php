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
        Schema::create('monthly_requirements', function (Blueprint $table) {
            $table->id();
            // Foreign key ke tabel raw_materials
            $table->foreignId('raw_material_id')->constrained('raw_materials')->onDelete('cascade');
            $table->unsignedSmallInteger('year'); // Tahun (misal: 2025)
            $table->unsignedTinyInteger('month'); // Bulan (1-12)
            $table->integer('total_monthly_usage'); // Total kebutuhan bulanan yang diinput user
            $table->integer('weekly_usage_1')->default(0); // Kebutuhan minggu ke-1 (hasil perhitungan)
            $table->integer('weekly_usage_2')->default(0); // Kebutuhan minggu ke-2 (hasil perhitungan)
            $table->integer('weekly_usage_3')->default(0); // Kebutuhan minggu ke-3 (hasil perhitungan)
            $table->integer('weekly_usage_4')->default(0); // Kebutuhan minggu ke-4 (hasil perhitungan)
            $table->timestamps();

            // Memastikan setiap raw_material hanya memiliki satu entri kebutuhan per bulan dan tahun tertentu
            $table->unique(['raw_material_id', 'year', 'month'], 'raw_material_monthly_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_requirements');
    }
};