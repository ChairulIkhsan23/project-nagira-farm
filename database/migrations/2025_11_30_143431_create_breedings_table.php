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
        Schema::create('breedings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ternak_id')->constrained('ternaks')->onDelete('cascade');
            $table->enum('status_reproduksi', ['kosong', 'kawin', 'bunting', 'nifas'])->default('kosong')->index();
            $table->date('tanggal_kawin')->nullable()->index();            
            $table->date('perkiraan_melahirkan')->nullable()->index();
            $table->integer('total_melahirkan')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('breedings');
    }
};
