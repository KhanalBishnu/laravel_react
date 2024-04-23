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
        Schema::table('test', function (Blueprint $table) {
            DB::statement("ALTER TABLE test MODIFY COLUMN testing_data ENUM('Cash', 'Credit') DEFAULT 'Cash'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('test', function (Blueprint $table) {
            //
        });
    }
};
