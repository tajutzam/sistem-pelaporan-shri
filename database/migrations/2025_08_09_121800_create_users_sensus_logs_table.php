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
        Schema::create('users_sensus_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            $table->enum('jenis', ['masuk', 'pindah', 'keluar']);

            $table->date('tanggal');

            $table->boolean('tidak_ada_pasien')->default(false);

            $table->timestamps();

            $table->unique(['user_id', 'tanggal', 'jenis']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_sensus_logs');
    }
};
