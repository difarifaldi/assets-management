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
        Schema::create('submission_forms_checkout_dates', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('submission_form_id');
            $table->date('loan_application_asset_date');
            $table->date('return_asset_date');

            $table->foreign('submission_form_id')->references('id')->on('submission_forms');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submission_forms_checkout_dates');
    }
};
