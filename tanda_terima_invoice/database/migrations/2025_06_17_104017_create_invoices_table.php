<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique(); // Nomor Invoice
            $table->date('invoice_date');              // Tanggal Invoice
            $table->string('customer_name');           // Nama Customer
            $table->string('tax_invoice_number')->nullable(); // Nomor Faktur Pajak (opsional)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};