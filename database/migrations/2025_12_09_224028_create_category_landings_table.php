<?php

// database/migrations/2025_01_02_000000_create_category_landings_table.php

use App\Models\Category;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('category_landings', function (Blueprint $table) {
            $table->id();

            // К какой категории относится посадочная
            $table->foreignIdFor(Category::class)
                ->constrained()
                ->cascadeOnDelete();

            // Название для админки
            $table->string('name');

            // Набор ID значений атрибутов, с которыми должна совпасть страница
            // Например: [id_пандора, id_автозапуск]
            $table->json('attribute_value_ids');

            // Текст посадочной (описание для страницы с этим фильтром)
            $table->text('content')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_landings');
    }
};
