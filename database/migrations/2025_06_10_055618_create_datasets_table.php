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
        Schema::create('datasets', function (Blueprint $table) {
            $table->id();
            $table->string('pasien');
            $table->foreignId('jenis_penyakit')
                ->constrained('jenis_penyakits');
            $table->foreignId('kelompok_usia')
                ->constrained('kelompok_usias');
            $table->string('jenis_kelamin');
            $table->tinyInteger('cluster')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('datasets');
    }
};
