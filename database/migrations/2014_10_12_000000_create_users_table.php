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
    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('email')->unique();
        $table->string('phone')->nullable();
        $table->date('birth_date')->nullable();
        $table->string('account_number')->nullable();
        $table->text('address')->nullable();
        $table->string('rt_rw')->nullable();
        $table->string('password');
        $table->enum('role', ['pengelola', 'nasabah'])->default('nasabah');
        // KUNCI UTAMA: Tambahkan kolom status
        $table->enum('status', ['pending', 'active', 'rejected'])->default('pending');
        $table->decimal('balance', 15, 2)->default(0);
        $table->rememberToken();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};