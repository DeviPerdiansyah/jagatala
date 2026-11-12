<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('withdrawals', function (Blueprint $table) {
            $table->enum('payment_method', ['cash', 'transfer'])->default('cash')->after('amount');
            $table->string('payment_detail')->nullable()->after('payment_method'); // Bank/E-Wallet & No Rek
            $table->date('pickup_date')->nullable()->after('status'); // Tanggal Nasabah mengambil/mencairkan
        });
    }

    public function down(): void
    {
        Schema::table('withdrawals', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'payment_detail', 'pickup_date']);
        });
    }
};