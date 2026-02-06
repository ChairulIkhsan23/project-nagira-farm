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
        Schema::create('kesehatans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ternak_id')->constrained('ternaks')->onDelete('cascade');

            $table->string('kondisi')->default('sehat')->index();
            $table->string('diagnosa')->nullable();
            $table->string('tindakan')->nullable();
            $table->string('obat')->nullable();

            $table->timestamp('tanggal_periksa')->nullable()->index();
            $table->timestamp('jadwal_vaksin_selanjutnya')->nullable()->index();

            $table->softDeletes();
            $table->timestamps();
        });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kesehatans');
    }
};
