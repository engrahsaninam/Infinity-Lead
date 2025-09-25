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
        Schema::create('csvs', function (Blueprint $table) {
            $table->id();
            $table->integer('list_id');
            $table->string('file');
            $table->string('type')->comment('google,excel');
            $table->integer('status')->default(0);
            $table->longText('headers');
            $table->string('mapping')->nullable();
            $table->integer('rows')->nullable();
            $table->integer('uploaded')->nullable();
            $table->string('spreadsheet_id')->nullable();
            $table->string('sheet_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('csvs');
    }
};
