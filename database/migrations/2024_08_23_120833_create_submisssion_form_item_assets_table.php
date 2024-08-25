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
        Schema::create('submisssion_form_item_assets', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('submission_form_id');
            $table->integer('assets_id');


            $table->foreign('submission_form_id')->references('id')->on('submission_forms');
            $table->foreign('assets_id')->references('id')->on('assets');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submisssion_form_item_assets');
    }
};
