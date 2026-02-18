<?php

namespace Tests\Feature;

use App\Facades\Seo;
use App\Models\Category;
use App\Models\SeoMask;
use App\Services\SeoApplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoMaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_seo_mask_applies_h1_pattern_for_category(): void
    {
        // Создаем категорию
        $category = Category::factory()->create(['name' => 'Тестовая категория']);

        // Создаем маску
        SeoMask::create([
            'context' => 'catalog_category',
            'maskable_type' => null,
            'maskable_id' => null,
            'meta_h1_pattern' => '{name} - тестовый H1',
            'meta_title_pattern' => '{category} - тестовый Title',
            'meta_description_pattern' => 'Описание для {category}',
        ]);

        // Очищаем SEO service
        app()->forgetInstance('seo');

        // Применяем SEO через SeoApplier
        $seoApplier = app(SeoApplier::class);
        $seoApplier->apply(
            model: $category,
            context: 'catalog_category',
            vars: [
                'name' => $category->name,
                'category' => $category->name,
                'filters' => '',
            ],
            useMask: true,
            maskModel: $category
        );

        // Проверяем, что маски применились
        $this->assertEquals('Тестовая категория - тестовый H1', Seo::getH1());
        $this->assertEquals('Тестовая категория - тестовый Title', Seo::getTitle());
        $this->assertEquals('Описание для Тестовая категория', Seo::getDescription());
    }

    public function test_seo_mask_applies_h1_pattern_with_filters(): void
    {
        // Создаем категорию
        $category = Category::factory()->create(['name' => 'Автосигнализации']);

        // Создаем маску
        SeoMask::create([
            'context' => 'catalog_category',
            'maskable_type' => null,
            'maskable_id' => null,
            'meta_h1_pattern' => '{name} {filters}',
        ]);

        // Очищаем SEO service
        app()->forgetInstance('seo');

        // Применяем SEO с фильтрами
        $seoApplier = app(SeoApplier::class);
        $seoApplier->apply(
            model: $category,
            context: 'catalog_category',
            vars: [
                'name' => $category->name,
                'category' => $category->name,
                'filters' => 'Производитель: Pandora',
            ],
            useMask: true,
            maskModel: $category
        );

        // Проверяем, что H1 содержит фильтры
        $this->assertEquals('Автосигнализации Производитель: Pandora', Seo::getH1());
    }

    public function test_seo_mask_removes_empty_placeholders(): void
    {
        // Создаем категорию
        $category = Category::factory()->create(['name' => 'Категория']);

        // Создаем маску с несуществующими плейсхолдерами
        SeoMask::create([
            'context' => 'catalog_category',
            'maskable_type' => null,
            'maskable_id' => null,
            'meta_h1_pattern' => '{name} {filters} {nonexistent}',
        ]);

        // Очищаем SEO service
        app()->forgetInstance('seo');

        // Применяем SEO без фильтров
        $seoApplier = app(SeoApplier::class);
        $seoApplier->apply(
            model: $category,
            context: 'catalog_category',
            vars: [
                'name' => $category->name,
                'category' => $category->name,
                'filters' => '',
            ],
            useMask: true,
            maskModel: $category
        );

        // Проверяем, что пустые плейсхолдеры удалены и лишние пробелы схлопнуты
        $this->assertEquals('Категория', Seo::getH1());
    }
}
