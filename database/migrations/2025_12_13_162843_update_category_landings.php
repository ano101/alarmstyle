<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('category_landings', function (Blueprint $table) {
            $table->string('attribute_value_ids_key', 255)->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::table('category_landings', function (Blueprint $table) {
            $table->dropColumn('attribute_value_ids_key');
        });
    }
};
