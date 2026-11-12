<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('withdrawals', function (Blueprint $table) {
            if (!Schema::hasColumn('withdrawals', 'payment_method')) {
                $table->enum('payment_method', ['cash', 'transfer'])->default('cash')->after('amount');
            }
            if (!Schema::hasColumn('withdrawals', 'payment_detail')) {
                $table->string('payment_detail')->nullable()->after('payment_method');
            }
            if (!Schema::hasColumn('withdrawals', 'pickup_date')) {
                $table->timestamp('pickup_date')->nullable()->after('payment_detail');
            }
            if (!Schema::hasColumn('withdrawals', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('withdrawals', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'payment_detail', 'pickup_date', 'rejection_reason']);
        });
    }
};