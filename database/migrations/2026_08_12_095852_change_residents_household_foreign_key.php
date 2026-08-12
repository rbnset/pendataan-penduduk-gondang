<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('residents', function (Blueprint $table) {
            $table->dropForeign(['household_id']);

            $table->foreign('household_id')
                ->references('id')
                ->on('households')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('residents', function (Blueprint $table) {
            $table->dropForeign(['household_id']);

            $table->foreign('household_id')
                ->references('id')
                ->on('households')
                ->cascadeOnDelete();
        });
    }
};