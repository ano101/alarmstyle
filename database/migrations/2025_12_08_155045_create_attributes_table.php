<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('attributes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignIdFor(\App\Models\AttributeGroup::class)->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('type');
            $table->text('helper_text')->nullable();
            $table->integer('sort')->default(0);
            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attributes');
    }
};
