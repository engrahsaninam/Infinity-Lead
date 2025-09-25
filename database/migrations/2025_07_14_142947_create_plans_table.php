<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Plan;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->double('price');
            $table->integer('currency_id');
            $table->integer('credits');
           $table->string('options', 1000)->default('{"email_max":"-1","list_max":"-1","subscriber_max":"-1","subscriber_per_list_max":"-1","campaign_max":"-1","automation_max":"-1","max_size_upload_total":"500","max_file_size_upload":"5"}');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
