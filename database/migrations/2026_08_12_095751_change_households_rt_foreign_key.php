<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('households', function (Blueprint $table) {
            $table->dropForeign(['rt_id']);

            $table->foreign('rt_id')
                ->references('id')
                ->on('rts')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('households', function (Blueprint $table) {
            $table->dropForeign(['rt_id']);

            $table->foreign('rt_id')
                ->references('id')
                ->on('rts')
                ->cascadeOnDelete();
        });
    }
};