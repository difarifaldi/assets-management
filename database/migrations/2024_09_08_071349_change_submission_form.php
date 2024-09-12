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
        Schema::table('submission_forms', function (Blueprint $table) {
            $table->integer('approved_by')->nullable()->change();
            $table->integer('approved_at')->nullable()->change();
            $table->timestamp('rejected_at')->nullable()->change();
            $table->integer('created_by')->nullable()->change();
            $table->integer('updated_by')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submission_forms');
    }
};
