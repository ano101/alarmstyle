<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('seo_masks', function (Blueprint $table) {
            $table->string('meta_h1_pattern')->nullable()->after('meta_description_pattern');
        });
    }

    public function down(): void
    {
        Schema::table('seo_masks', function (Blueprint $table) {
            $table->dropColumn('meta_h1_pattern');
        });
    }
};
