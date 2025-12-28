<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('catalog_quick_links', function (Blueprint $table) {
            $table->id();

            // к чему привязана кнопка (Category или CategoryLanding)
            $table->morphs('linkable'); // linkable_type, linkable_id

            // как отображать
            $table->string('label');
            $table->unsignedInteger('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('color')->nullable(); // optional: primary/success/danger etc.
            $table->string('icon')->nullable();  // optional: heroicon-o-...

            // куда ведёт
            $table->string('type'); // 'catalog_category' | 'catalog_landing' | 'catalog_path' | 'custom_url'
            $table->string('path')->nullable();  // для type=catalog_path (например "alarms/pandora/gsm")
            $table->string('url')->nullable();   // для type=custom_url

            $table->timestamps();

            $table->index(['linkable_type', 'linkable_id', 'is_active', 'sort'], 'cql_linkable_active_sort');
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('catalog_quick_links');
    }
};
