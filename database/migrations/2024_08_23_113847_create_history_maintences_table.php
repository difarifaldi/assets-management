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
        Schema::create('riwayat_perawatan', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id')->autoIncrement();
            $table->integer('assets_id');
            $table->date('date');
            $table->integer('status');
            $table->text('description')->nullable();
            $table->json('attachment');
            $table->tinyInteger('latest')->nullable();
            $table->integer('created_by');
            $table->timestamp('created_at')->useCurrent();
            $table->integer('updated_by');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->integer('deleted_by')->nullable();
            $table->timestamp('deleted_at')->nullable();


            $table->foreign('assets_id')->references('id')->on('aset');
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
        Schema::dropIfExists('riwayat_perawatan');
    }
};
