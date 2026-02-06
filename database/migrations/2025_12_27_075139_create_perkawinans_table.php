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
        Schema::create('perkawinans', function (Blueprint $table) {
        $table->id();
        $table->foreignId('betina_id')
            ->constrained('ternaks')
            ->cascadeOnDelete();
        $table->foreignId('pejantan_id')
            ->nullable()       
            ->constrained('ternaks')
            ->nullOnDelete();
        $table->date('tanggal_kawin')->nullable();
        $table->enum('jenis_kawin', ['alami', 'IB']);
        $table->enum('hasil', ['pending', 'bunting', 'gagal'])
            ->default('pending');
        $table->string('keterangan')->nullable();
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perkawinans');
    }
};
