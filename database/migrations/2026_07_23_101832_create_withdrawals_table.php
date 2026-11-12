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
    Schema::create('withdrawals', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Nasabah penarik
        $table->decimal('amount', 15, 2); // Jumlah yang ditarik
        $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending'); // Status penarikan
        $table->text('notes')->nullable(); // Alasan penolakan (opsional)
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withdrawals');
    }
};