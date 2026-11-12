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
    Schema::create('transactions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Nasabah
        $table->foreignId('category_id')->constrained()->onDelete('cascade'); // Jenis Sampah
        $table->decimal('weight', 8, 2); // Berat dalam KG (bisa desimal, misal 1.5 kg)
        $table->decimal('price_per_kg', 12, 2); // Harga per kg saat transaksi
        $table->decimal('total_amount', 15, 2); // Total rupiah yang didapat
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};