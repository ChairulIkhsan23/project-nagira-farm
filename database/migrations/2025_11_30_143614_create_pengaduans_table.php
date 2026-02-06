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
        Schema::create('pengaduans', function (Blueprint $table) {
            $table->id();

            $table->string('nama_pengirim');
            $table->string('email')->nullable();
            $table->string('kategori')->index();
            $table->string('subjek')->nullable();
            $table->text('pesan');
            $table->string('status')->default('baru')->index();
            $table->text('catatan_admin')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaduans');
    }
};
