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
        Schema::create('shris', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pasien_id')->constrained('pasiens')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('dpjp_id')->constrained('dpjps')->cascadeOnUpdate()->cascadeOnDelete();
            $table->enum('status', ['masuk', 'pindah', 'keluar']);
            $table->date('tanggal_masuk');
            $table->string('asal_pasien');
            $table->string('ruang_perawatan');
            $table->foreignId('kelas_perawatan_id')->constrained('ruangans')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('jenis_penjaminan_id')->constrained('penjaminans')->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shris');
    }
};
