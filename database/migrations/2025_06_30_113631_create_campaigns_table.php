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
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('status')->default(0);
            $table->integer('user_id');
            $table->string('controls')->default('skip_responded,skip_duplicates');
            $table->integer('list_id')->nullable();
            $table->integer('template_id')->nullable();
            $table->string('days')->default('mon,tue,wed,thu,fri');
            $table->string('day')->default('full');
            $table->time('start')->default('08:00:00');
            $table->time('end')->default('18:00:00');
            $table->string('timezone')->nullable();
            $table->string('deliverability')->default('plain_text_emails, gradual_sending, auto_match_inboxes, avoid_bounced_emails');
            $table->string('subject')->nullable();
            $table->string('attachment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
