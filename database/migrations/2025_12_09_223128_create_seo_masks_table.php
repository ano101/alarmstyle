<?php

// database/migrations/2025_01_01_000000_create_seo_masks_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('seo_masks', function (Blueprint $table) {
            $table->id();

            // Контекст, где используется маска:
            // например: "catalog_category", "product_page" и т.п.
            $table->string('context');

            // Морф-связь: конкретная модель (Category, Product и т.д.)
            // Для глобальной маски по контексту оставляем NULL
            $table->nullableMorphs('maskable');

            // Шаблоны (маски) для title и description
            // Например:
            //  meta_title_pattern: "{category} – {filters} – страница {page}"
            //  meta_description_pattern: "Каталог {category_lc}. Выбрано: {filters}. Страница {page}."
            $table->string('meta_title_pattern')->nullable();
            $table->text('meta_description_pattern')->nullable();

            // На будущее, если захочешь дополнительные поля
            $table->json('extra')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seo_masks');
    }
};
