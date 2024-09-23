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
        Schema::table('history_assigns', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('submission_forms_id')->nullable();

            $table->foreign('submission_forms_id')->references('id')->on('submission_forms');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
