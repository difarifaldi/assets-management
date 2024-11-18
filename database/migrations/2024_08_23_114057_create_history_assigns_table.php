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
        Schema::create('riwayat_penugasan', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id')->autoIncrement();
            $table->integer('id_aset');
            $table->integer('ditugaskan_ke')->nullable();
            $table->timestamp('ditugaskan_pada')->nullable();
            $table->integer('dikembalikan_oleh')->nullable();
            $table->timestamp('dikembalikan_pada')->nullable();
            $table->json('lampiran');
            $table->tinyInteger('latest')->nullable();
            $table->integer('created_by');
            $table->timestamp('created_at')->useCurrent();
            $table->integer('updated_by');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->integer('deleted_by')->nullable();
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('id_aset')->references('id')->on('aset');
            $table->foreign('ditugaskan_ke')->references('id')->on('pengguna');
            $table->foreign('dikembalikan_oleh')->references('id')->on('pengguna');
            $table->foreign('created_by')->references('id')->on('pengguna');
            $table->foreign('updated_by')->references('id')->on('pengguna');
            $table->foreign('deleted_by')->references('id')->on('pengguna');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_penugasan');
    }
};
