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
        Schema::create('aset', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id')->autoIncrement();
            $table->integer('id_kategori_aset')->nullable();
            $table->tinyInteger('tipe')->comment('1 as Physical Asset and 2 as non Physical Asset');
            $table->string('barcode_code');
            $table->string('nama');
            $table->integer('status');
            $table->bigInteger('nilai')->nullable();
            $table->date('expired_pada')->nullable();
            $table->text('deskripsi')->nullable();
            $table->json('lampiran')->nullable();
            $table->integer('id_brand');
            $table->date('tanggal_pengambilan')->nullable();
            $table->date('tanggal_akhir_garansi')->nullable();
            $table->integer('durasi_garansi')->nullable();
            $table->integer('ditugaskan_ke')->nullable();
            $table->timestamp('ditugaskan_pada')->nullable();
            $table->integer('pengembalian_oleh')->nullable();
            $table->timestamp('pengembalian_pada')->nullable();
            $table->integer('dipinjam_oleh')->nullable();
            $table->timestamp('dipinjam_pada')->nullable();
            $table->integer('created_by');
            $table->timestamp('created_at')->useCurrent();
            $table->integer('updated_by');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->integer('deleted_by')->nullable();
            $table->timestamp('deleted_at')->nullable();


            $table->foreign('id_kategori_aset')->references('id')->on('kategori_aset');
            $table->foreign('ditugaskan_ke')->references('id')->on('pengguna');
            $table->foreign('pengembalian_oleh')->references('id')->on('pengguna');
            $table->foreign('dipinjam_oleh')->references('id')->on('pengguna');
            $table->foreign('created_by')->references('id')->on('pengguna');
            $table->foreign('updated_by')->references('id')->on('pengguna');
            $table->foreign('deleted_by')->references('id')->on('pengguna');
            $table->foreign('id_brand')->references('id')->on('brand');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aset');
    }
};
