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
        Schema::table('shifts', function (Blueprint $table): void {
            $table->index(['scheduled_date', 'begin_time']);
            $table->index(['status', 'scheduled_date']);
            $table->index(['department', 'scheduled_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shifts', function (Blueprint $table): void {
            $table->dropIndex(['scheduled_date', 'begin_time']);
            $table->dropIndex(['status', 'scheduled_date']);
            $table->dropIndex(['department', 'scheduled_date']);
        });
    }
};
