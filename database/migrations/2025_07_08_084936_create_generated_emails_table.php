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
        Schema::create('generated_emails', function (Blueprint $table) {
            $table->id();
            $table->integer('linked_in_export_data_id');
            $table->string('email');
            $table->enum('status', ['valid', 'invalid', 'guessed'])->default('guessed');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('generated_emails');
    }
};
