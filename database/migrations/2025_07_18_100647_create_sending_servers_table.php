<?php

use App\Models\SendingServer;
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
        Schema::create('sending_servers', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->text('type');
            $table->string('api_key')->nullable();
            $table->string('name')->nullable();
            $table->string('domain');
            $table->string('host');
            $table->string('email')->nullable();
            $table->string('username');
            $table->string('password');
            $table->string('port');
            $table->string('encryption');
            $table->integer('status')->default(SendingServer::STATUS_UNVERIFIED);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sending_servers');
    }
};
