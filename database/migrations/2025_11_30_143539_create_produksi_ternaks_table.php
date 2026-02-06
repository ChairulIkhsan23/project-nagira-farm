<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('produksi_ternaks', function (Blueprint $table) {
            $table->id();

            $table->string('jenis_produksi')->index();
            $table->foreignId('ternak_id')->nullable()->constrained('ternaks')->nullOnDelete();

            $table->integer('jumlah')->default(1);
            $table->string('keterangan')->nullable();
            $table->timestamp('tanggal')->index();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produksi_ternaks');
    }
};
