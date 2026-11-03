<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shift_tasks', function (Blueprint $table): void {
            $table->dropForeign(['shift_id']);
            $table->unsignedBigInteger('shift_id')->nullable()->change();
            $table->foreign('shift_id')->references('id')->on('shifts')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('shift_tasks', function (Blueprint $table): void {
            $table->dropForeign(['shift_id']);
            $table->unsignedBigInteger('shift_id')->nullable(false)->change();
            $table->foreign('shift_id')->references('id')->on('shifts')->cascadeOnDelete();
        });
    }
};
