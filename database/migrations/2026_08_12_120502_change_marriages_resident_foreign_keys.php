<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('marriages', function (Blueprint $table) {
            $table->dropForeign(['husband_resident_id']);
            $table->dropForeign(['wife_resident_id']);

            $table->foreign('husband_resident_id')
                ->references('id')
                ->on('residents')
                ->restrictOnDelete();

            $table->foreign('wife_resident_id')
                ->references('id')
                ->on('residents')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('marriages', function (Blueprint $table) {
            $table->dropForeign(['husband_resident_id']);
            $table->dropForeign(['wife_resident_id']);

            $table->foreign('husband_resident_id')
                ->references('id')
                ->on('residents')
                ->cascadeOnDelete();

            $table->foreign('wife_resident_id')
                ->references('id')
                ->on('residents')
                ->cascadeOnDelete();
        });
    }
};