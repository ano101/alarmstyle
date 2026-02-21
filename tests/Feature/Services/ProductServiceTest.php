<?php

namespace Tests\Feature\Services;

use App\Models\Attribute;
use App\Models\AttributeGroup;
use App\Models\AttributeValue;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Services\ProductService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductServiceTest extends TestCase
{
    use RefreshDatabase;

    private ProductService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(ProductService::class);
    }

    // ─── buildProductData: базовые поля ────────────────────────────────────

    public function test_build_product_data_returns_required_keys(): void
    {
        $product = Product::create(['name' => 'Pandora DX 9X']);

        $data = $this->service->buildProductData($product);

        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('name', $data);
        $this->assertArrayHasKey('price', $data);
        $this->assertArrayHasKey('gallery', $data);
        $this->assertArrayHasKey('attributeGroups', $data);
        $this->assertArrayHasKey('attributeFeature', $data);
    }

    public function test_build_product_data_maps_name_and_id(): void
    {
        $product = Product::create(['name' => 'Starline A96']);

        $data = $this->service->buildProductData($product);

        $this->assertEquals($product->id, $data['id']);
        $this->assertEquals('Starline A96', $data['name']);
    }

    public function test_build_product_data_price_is_null_without_base_price(): void
    {
        $product = Product::create(['name' => 'Без цены']);

        $data = $this->service->buildProductData($product);

        $this->assertNull($data['price']);
    }

    public function test_build_product_data_returns_base_price(): void
    {
        $product = Product::create(['name' => 'С ценой']);
        ProductPrice::create([
            'product_id' => $product->id,
            'price' => 12500,
            'type' => ProductPrice::TYPE_BASE,
        ]);
        $product->load('basePrice');

        $data = $this->service->buildProductData($product);

        $this->assertEquals(12500, $data['price']);
    }

    // ─── buildProductData: галерея ──────────────────────────────────────────

    public function test_build_product_data_gallery_is_empty_without_images(): void
    {
        $product = Product::create(['name' => 'Без фото']);

        $data = $this->service->buildProductData($product);

        $this->assertEmpty($data['gallery']);
    }

    public function test_build_product_data_puts_main_image_first(): void
    {
        $product = Product::create(['name' => 'С главным фото', 'image' => 'products/main.jpg']);

        $data = $this->service->buildProductData($product);

        $this->assertEquals('products/main.jpg', $data['gallery'][0]);
    }

    // ─── buildProductData: атрибуты ─────────────────────────────────────────

    public function test_build_product_data_groups_attributes_by_group_name(): void
    {
        $group = AttributeGroup::create(['name' => 'Безопасность']);
        $attribute = Attribute::create([
            'name' => 'Диалоговый код',
            'type' => 1,
            'type_front' => 1,
            'attribute_group_id' => $group->id,
            'in_filter' => true,
            'sort' => 1,
        ]);
        $value = AttributeValue::create([
            'attribute_id' => $attribute->id,
            'value' => 'Диалоговый-Есть',
        ]);

        $product = Product::create(['name' => 'Тест атрибутов']);
        $product->attributeValues()->attach($value->id);
        $product->load('attributeValues.attribute.attributeGroup');

        $data = $this->service->buildProductData($product);

        $this->assertCount(1, $data['attributeGroups']);
        $this->assertEquals('Безопасность', $data['attributeGroups'][0]['name']);
        $this->assertCount(1, $data['attributeGroups'][0]['attributes']);
        $this->assertEquals('Диалоговый код', $data['attributeGroups'][0]['attributes'][0]['name']);
        $this->assertEquals('Диалоговый-Есть', $data['attributeGroups'][0]['attributes'][0]['value']);
    }

    public function test_build_product_data_uses_fallback_group_name_when_no_group(): void
    {
        $group = AttributeGroup::create(['name' => 'Прочее']);
        $attribute = Attribute::create([
            'name' => 'Одиночный атрибут',
            'type' => 1,
            'type_front' => 1,
            'attribute_group_id' => $group->id,
            'in_filter' => true,
            'sort' => 1,
        ]);
        $value = AttributeValue::create([
            'attribute_id' => $attribute->id,
            'value' => 'Значение',
        ]);

        $product = Product::create(['name' => 'Тест без группы']);
        $product->attributeValues()->attach($value->id);
        $product->load('attributeValues.attribute.attributeGroup');

        // Имитируем отсутствие группы — сбрасываем relation через setRelation
        $product->attributeValues->each(function ($av) {
            $av->attribute->setRelation('attributeGroup', null);
        });

        $data = $this->service->buildProductData($product);

        $this->assertEquals('Без группы', $data['attributeGroups'][0]['name']);
    }

    public function test_build_product_data_collects_features_from_attribute_values(): void
    {
        $group = AttributeGroup::create(['name' => 'Функции']);
        $attribute = Attribute::create([
            'name' => 'GPS',
            'type' => 1,
            'type_front' => 1,
            'attribute_group_id' => $group->id,
            'in_filter' => true,
            'sort' => 1,
        ]);
        $value = AttributeValue::create([
            'attribute_id' => $attribute->id,
            'value' => 'GPS-Есть',
            'feature' => 'gps_icon',
        ]);

        $product = Product::create(['name' => 'С фичей']);
        $product->attributeValues()->attach($value->id);
        $product->load('attributeValues.attribute.attributeGroup');

        $data = $this->service->buildProductData($product);

        $this->assertContains('gps_icon', $data['attributeFeature']);
    }

    public function test_build_product_data_skips_values_with_missing_attribute(): void
    {
        $group = AttributeGroup::create(['name' => 'Temp']);
        $attribute = Attribute::create([
            'name' => 'Temp attr',
            'type' => 1,
            'type_front' => 1,
            'attribute_group_id' => $group->id,
            'in_filter' => true,
            'sort' => 1,
        ]);
        $value = AttributeValue::create([
            'attribute_id' => $attribute->id,
            'value' => 'Orphan',
        ]);

        $product = Product::create(['name' => 'Orphan value']);
        $product->attributeValues()->attach($value->id);
        $product->load('attributeValues.attribute.attributeGroup');

        // Имитируем отсутствие атрибута — сбрасываем relation
        $product->attributeValues->each(fn ($av) => $av->setRelation('attribute', null));

        $data = $this->service->buildProductData($product);

        $this->assertEmpty($data['attributeGroups']);
        $this->assertEmpty($data['attributeFeature']);
    }

    public function test_build_product_data_attribute_includes_helper_text(): void
    {
        $group = AttributeGroup::create(['name' => 'Установка']);
        $attribute = Attribute::create([
            'name' => 'Тип установки',
            'type' => 1,
            'type_front' => 1,
            'attribute_group_id' => $group->id,
            'in_filter' => true,
            'sort' => 1,
            'helper_text' => 'Уточните у менеджера',
        ]);
        $value = AttributeValue::create([
            'attribute_id' => $attribute->id,
            'value' => 'Скрытая',
        ]);

        $product = Product::create(['name' => 'Тест helper_text']);
        $product->attributeValues()->attach($value->id);
        $product->load('attributeValues.attribute.attributeGroup');

        $data = $this->service->buildProductData($product);

        $this->assertEquals('Уточните у менеджера', $data['attributeGroups'][0]['attributes'][0]['helper_text']);
    }
}
