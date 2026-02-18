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
        Schema::create('shri_pindah', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shri_id')->references('id')->on('shris')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('kelas_perawatan_id')->constrained('ruangans')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('lama_dirawat');
            $table->date('tanggal_pindah');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shri_pindah');
    }
};
