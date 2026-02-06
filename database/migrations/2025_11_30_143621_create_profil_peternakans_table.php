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
        Schema::create('profil_peternakans', function (Blueprint $table) {
            $table->id();

            $table->text('deskripsi')->nullable();
            $table->string('foto_profil')->nullable();

            $table->string('kontak_wa')->nullable();
            $table->string('email')->nullable();
            $table->string('lokasi_maps')->nullable();

            $table->text('visi')->nullable();
            $table->text('misi')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profil_peternakans');
    }
};
