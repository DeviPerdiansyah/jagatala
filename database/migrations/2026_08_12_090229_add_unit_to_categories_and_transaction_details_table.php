<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('unit', 10)->default('kg')->after('type'); // 'kg' atau 'pcs'
        });

        Schema::table('transaction_details', function (Blueprint $table) {
            $table->string('unit', 10)->default('kg')->after('weight');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('unit');
        });

        Schema::table('transaction_details', function (Blueprint $table) {
            $table->dropColumn('unit');
        });
    }
};