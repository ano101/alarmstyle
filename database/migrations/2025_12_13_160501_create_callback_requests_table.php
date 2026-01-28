<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('callback_requests', function (Blueprint $table) {
            $table->id();

            $table->string('name')->nullable();
            $table->string('phone', 50);
            $table->text('comment')->nullable();

            $table->string('page_url')->nullable();
            $table->json('utm')->nullable();

            $table->string('status', 20)->default('new'); // new|processed|spam etc.
            $table->timestamps();

            $table->index(['status', 'created_at'], 'cb_status_created');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('callback_requests');
    }
};
