<?php

use App\Models\Subscription;
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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('plan_id');
            $table->unsignedBigInteger('payment_method_id')->nullable();
            $table->dateTime('start');
            $table->dateTime('end');
            $table->string('status')->default(Subscription::STATUS_PENDING);
            $table->string('reject_reason')->nullable();
            $table->longText('payment_response')->nullable();
            $table->integer('price');
            $table->string('code');
            $table->integer('credits');
            $table->integer('recurring')->default(Subscription::IS_RECURRING);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
