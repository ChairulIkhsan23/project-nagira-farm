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
        Schema::create('ternaks', function (Blueprint $table) {
            $table->id();
            
            $table->string('kode_ternak')->unique();
            $table->string('nama_ternak')->nullable();
            $table->string('jenis_ternak')->index(); 
            $table->enum('kategori', ['breeding', 'fattening'])->nullable()->index();
            $table->enum('jenis_kelamin', ['jantan', 'betina'])->index();

            $table->date('tanggal_lahir')->nullable()->index();
            $table->string('foto')->nullable();

            $table->string('status_kesehatan')->default('sehat')->index();
            $table->string('status_aktif')->default('aktif')->index();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ternaks');
    }
};
