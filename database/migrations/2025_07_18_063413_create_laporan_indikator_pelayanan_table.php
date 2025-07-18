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
        Schema::create('laporan_indikator_pelayanan', function (Blueprint $table) {
            $table->id();

            $table->string('kode_laporan', 20)->unique();
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->decimal('jumlah_hari', 10, 6);
            $table->boolean('default_used')->default(false);

            $table->enum('status', ['draft', 'pending', 'approved', 'rejected'])->default('draft');
            $table->text('catatan')->nullable();

            $table->unsignedBigInteger('dibuat_oleh');
            $table->unsignedBigInteger('disetujui_oleh')->nullable();
            $table->timestamp('tanggal_disetujui')->nullable();
            $table->timestamp('tanggal_dikirim')->nullable();

            $table->timestamps();

            // Foreign Keys
            $table->foreign('dibuat_oleh')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('disetujui_oleh')->references('id')->on('users')->onDelete('set null');

            // Indexes
            $table->index(['tanggal_mulai', 'tanggal_selesai']);
            $table->index('status');
            $table->index('dibuat_oleh');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_indikator_pelayanan');
    }
};
