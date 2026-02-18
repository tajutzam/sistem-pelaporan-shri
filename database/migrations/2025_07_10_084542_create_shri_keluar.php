<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shri_keluar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shri_id')->references('id')->on('shris')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('dpjp_id')->constrained('dpjps')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('diagnosa_id')->constrained('diagnosas')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('cara_keluar');
            $table->string('lama_dirawat');
            $table->date('tanggal_keluar');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shri_keluar');
    }
};
