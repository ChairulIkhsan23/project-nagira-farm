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
        Schema::create('artikels', function (Blueprint $table) {
            $table->id();

            $table->foreignId('kategori_id')->nullable()->constrained('kategori_artikels')->nullOnDelete();
            $table->string('judul')->index();
            $table->string('foto')->nullable();
            $table->longText('isi');
            $table->string('status')->default('draft')->index();
            $table->timestamp('tanggal_publish')->nullable()->index();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('artikels');
    }
};
