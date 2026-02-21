<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductImage;
use App\Services\ProductService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductImageSortingTest extends TestCase
{
    use RefreshDatabase;

    public function test_images_are_ordered_by_sort_order(): void
    {
        $product = Product::create(['name' => 'Тест сортировки']);

        ProductImage::create(['product_id' => $product->id, 'url' => 'third.jpg', 'sort_order' => 3]);
        ProductImage::create(['product_id' => $product->id, 'url' => 'first.jpg', 'sort_order' => 1]);
        ProductImage::create(['product_id' => $product->id, 'url' => 'second.jpg', 'sort_order' => 2]);

        $product->load('images');
        $urls = $product->images->pluck('url')->all();

        $this->assertEquals(['first.jpg', 'second.jpg', 'third.jpg'], $urls);
    }

    public function test_sort_order_auto_increments_on_create(): void
    {
        $product = Product::create(['name' => 'Тест автоинкремента']);

        $img1 = ProductImage::create(['product_id' => $product->id, 'url' => 'a.jpg']);
        $img2 = ProductImage::create(['product_id' => $product->id, 'url' => 'b.jpg']);
        $img3 = ProductImage::create(['product_id' => $product->id, 'url' => 'c.jpg']);

        $this->assertEquals(1, $img1->sort_order);
        $this->assertEquals(2, $img2->sort_order);
        $this->assertEquals(3, $img3->sort_order);
    }

    public function test_gallery_in_product_service_puts_main_image_first_then_additional(): void
    {
        $product = Product::create(['name' => 'Галерея', 'image' => 'main.jpg']);

        ProductImage::create(['product_id' => $product->id, 'url' => 'extra1.jpg', 'sort_order' => 1]);
        ProductImage::create(['product_id' => $product->id, 'url' => 'extra2.jpg', 'sort_order' => 2]);

        $product->load('images');

        $service = app(ProductService::class);
        $data = $service->buildProductData($product);

        $this->assertEquals('main.jpg', $data['gallery'][0]);
        $this->assertContains('extra1.jpg', $data['gallery']);
        $this->assertContains('extra2.jpg', $data['gallery']);
    }
}
