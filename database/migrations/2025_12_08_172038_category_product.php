<?php

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('category_product', function (Blueprint $table) {
            $table->foreignIdFor(Product::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignIdFor(Category::class)
                ->constrained()
                ->cascadeOnDelete();

            // главное поле
            $table->boolean('is_main')->default(false);

            $table->timestamps();

            // ключ для уникальности
            $table->primary(['product_id', 'category_id']);
        });
    }

    public function down(): void
    {
        Schema::table('category_product', function (Blueprint $table) {
            //
        });
    }
};
