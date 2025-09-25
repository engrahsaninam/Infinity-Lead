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
        Schema::create('analytics', function (Blueprint $table) {
            $table->id();
            $table->integer('campaign_id');
            $table->integer('subscriber_id');
            $table->integer('email_account_id')->nullable();
            $table->datetime('sent_at')->nullable();
            $table->string('response')->nullable();
            $table->longText('error')->nullable();
            $table->integer('sent')->default(0);
            $table->integer('replied')->default(0);
            $table->integer('skipped')->default(0);
            $table->integer('open')->default(0);
            $table->integer('blacklisted')->default(0);
            $table->integer('bounced')->default(0);
            $table->integer('followup_1')->nullable();
            $table->integer('followup_2')->nullable();
            $table->unique(['campaign_id', 'subscriber_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analytics');
    }
};
