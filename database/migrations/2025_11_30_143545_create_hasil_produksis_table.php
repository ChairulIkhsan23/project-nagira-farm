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
        Schema::create('hasil_produksis', function (Blueprint $table) {
            $table->id();

            $table->string('jenis_produk')->index();
            $table->integer('jumlah');
            $table->string('satuan')->default('liter');
            $table->string('foto')->nullable();
            $table->text('deskripsi')->nullable();
            $table->timestamp('tanggal_produksi')->index();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hasil_produksis');
    }
};
