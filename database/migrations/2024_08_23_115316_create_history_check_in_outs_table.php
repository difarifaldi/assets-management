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
        Schema::create('history_check_in_outs', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id')->autoIncrement();
            $table->integer('assets_id');
            $table->integer('check_in_by')->nullable();
            $table->timestamp('check_in_at')->nullable();
            $table->integer('check_out_by')->nullable();
            $table->timestamp('check_out_at')->nullable();
            $table->json('attachment');
            $table->tinyInteger('latest')->nullable();
            $table->integer('created_by');
            $table->timestamp('created_at')->useCurrent();
            $table->integer('updated_by');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->integer('deleted_by')->nullable();
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('assets_id')->references('id')->on('assets');
            $table->foreign('check_in_by')->references('id')->on('users');
            $table->foreign('check_out_by')->references('id')->on('users');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            $table->foreign('deleted_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history_check_in_outs');
    }
};
