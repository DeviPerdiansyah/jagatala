<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('withdrawals', function (Blueprint $table) {
            $table->timestamp('completed_at')->nullable()->after('status'); // Waktu uang fisik/transfer diserahkan
            $table->longText('signature')->nullable()->after('completed_at'); // Data Canvas Base64 Tanda Tangan
        });
    }

    public function down(): void
    {
        Schema::table('withdrawals', function (Blueprint $table) {
            $table->dropColumn(['completed_at', 'signature']);
        });
    }
};