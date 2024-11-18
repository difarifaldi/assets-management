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
        Schema::create('form_pengajuan_aset', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id_form_pengajuan');
            $table->integer('id_aset');

            $table->foreign('id_form_pengajuan')->references('id')->on('form_pengajuan');
            $table->foreign('id_aset')->references('id')->on('aset');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_pengajuan_aset');
    }
};
