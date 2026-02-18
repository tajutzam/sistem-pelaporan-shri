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
        Schema::create('laporan_indikator_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('laporan_id');

            // Data Ruangan
            $table->unsignedBigInteger('ruangan_id');
            $table->string('nama_ruangan', 100);
            $table->integer('jumlah_tempat_tidur');

            // Data Periode
            $table->decimal('jumlah_periode', 15, 6);

            // Data Perawatan
            $table->integer('jumlah_hari_perawatan');
            $table->integer('total_lama_dirawat');

            // Data Pasien Keluar
            $table->integer('pasien_keluar_hidup');
            $table->integer('pasien_keluar_mati');
            $table->integer('total_pasien_keluar');

            // Indikator Pelayanan
            $table->decimal('bor', 8, 2); // Bed Occupancy Rate
            $table->decimal('avlos', 8, 2); // Average Length of Stay
            $table->decimal('bto', 8, 2); // Bed Turn Over
            $table->decimal('toi', 8, 2); // Turn Over Interval
            $table->decimal('gdr', 8, 2); // Gross Death Rate
            $table->decimal('ndr', 8, 2);


            // Foreign Keys
            $table->foreign('laporan_id')->references('id')->on('laporan_indikator_pelayanan')->onDelete('cascade');
            $table->foreign('ruangan_id')->references('id')->on('ruangans')->onDelete('cascade');

            // Indexes
            $table->index(['laporan_id', 'ruangan_id']);
            $table->index('ruangan_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_indikator_detail');
    }
};
