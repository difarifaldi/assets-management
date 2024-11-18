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
        Schema::create('form_pengajuan', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id')->autoIncrement();
            $table->text('deskripsi');
            $table->tinyInteger('tipe')->comment('1: submission assign, 2: submission check in asset');
            $table->text('lampiran')->nullable();
            $table->integer('diterima_oleh');
            $table->integer('diterima_pada');
            $table->integer('ditolak_oleh')->nullable();
            $table->timestamp('ditolak_pada')->nullable();
            $table->text('alasan')->nullable();
            $table->integer('created_by')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->integer('updated_by')->nullable();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->integer('deleted_by')->nullable();
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('diterima_oleh')->references('id')->on('pengguna');
            $table->foreign('ditolak_oleh')->references('id')->on('pengguna');
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
        Schema::dropIfExists('form_pengajuan');
    }
};
