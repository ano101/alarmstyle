<?php

namespace App\Console\Commands;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\Console\Command\Command as CommandAlias;
use Symfony\Component\DomCrawler\Crawler;

class ImportAutostudioProducts extends Command
{
    protected $signature = 'autostudio:import';
    protected $description = 'Импорт сигнализаций Pandora с autostudio.ru';

    public function handle(): int
    {
        $urls = [
            'https://www.autostudio.ru/catalog/ustanovka-avtosignalizaciy/pandora/dx_40r.html',
            'https://www.autostudio.ru/catalog/ustanovka-avtosignalizaciy/pandora/dx-40rs.html',
            'https://www.autostudio.ru/catalog/ustanovka-avtosignalizaciy/pandora/dx_57r.html',
            'https://www.autostudio.ru/catalog/ustanovka-avtosignalizaciy/pandora/pandora_dx_6x-lora.html',
            'https://www.autostudio.ru/catalog/ustanovka-avtosignalizaciy/pandora/pandora_dx_9x_lora.html',
            'https://www.autostudio.ru/catalog/ustanovka-avtosignalizaciy/pandora/dx-91-lora.html',
            'https://www.autostudio.ru/catalog/ustanovka-avtosignalizaciy/pandora/dxl-4710.html',
            'https://www.autostudio.ru/catalog/ustanovka-avtosignalizaciy/pandora/pandora_vx_4g-v3.html',
            'https://www.autostudio.ru/catalog/ustanovka-avtosignalizaciy/pandora/vx-4g_gps_v3.html',
            'https://www.autostudio.ru/catalog/ustanovka-avtosignalizaciy/pandora/vx-4g-gps-fd-light.html',
            'https://www.autostudio.ru/catalog/ustanovka-avtosignalizaciy/pandora/vx-4g_gps_v3_fd.html',
            'https://www.autostudio.ru/catalog/ustanovka-avtosignalizaciy/pandora/vx_3100.html',
            'https://www.autostudio.ru/catalog/ustanovka-avtosignalizaciy/pandora/vx46-moto-evo.html',
            'https://www.autostudio.ru/catalog/ustanovka-avtosignalizaciy/pandora/uf-4-g.html',
            'https://www.autostudio.ru/catalog/ustanovka-avtosignalizaciy/pandora/uf-4g_bt.html',
            'https://www.autostudio.ru/catalog/ustanovka-avtosignalizaciy/pandora/pandora_ux_4100_fd.html',
            'https://www.autostudio.ru/catalog/ustanovka-avtosignalizaciy/pandora/ux-4110v2.html',
            'https://www.autostudio.ru/catalog/ustanovka-avtosignalizaciy/pandora/pandora_ux_4790.html',
            'https://www.autostudio.ru/catalog/ustanovka-avtosignalizaciy/pandora/commander.html',
            'https://www.autostudio.ru/catalog/ustanovka-avtosignalizaciy/pandora/4g-v3-light.html',
            'https://www.autostudio.ru/catalog/ustanovka-avtosignalizaciy/starline/item4220.html',
            'https://www.autostudio.ru/catalog/ustanovka-avtosignalizaciy/starline/a93-2can-2lin_v2.html',
            'https://www.autostudio.ru/catalog/ustanovka-avtosignalizaciy/starline/item4222.html',
            'https://www.autostudio.ru/catalog/ustanovka-avtosignalizaciy/starline/item4221.html',
            'https://www.autostudio.ru/catalog/ustanovka-avtosignalizaciy/starline/a63v2.html',
            'https://www.autostudio.ru/catalog/ustanovka-avtosignalizaciy/starline/a60eco.html',
            'https://www.autostudio.ru/catalog/ustanovka-avtosignalizaciy/starline/a63v2-ecov2.html',
            'https://www.autostudio.ru/catalog/ustanovka-avtosignalizaciy/starline/a63_v2_2can_2lin_eco.html',
            'https://www.autostudio.ru/catalog/ustanovka-avtosignalizaciy/starline/a93_v2-eco.html',
            'https://www.autostudio.ru/catalog/ustanovka-avtosignalizaciy/starline/a93v2.html',
            'https://www.autostudio.ru/catalog/ustanovka-avtosignalizaciy/starline/a93-v2-2can-2lin-eco.html',
            'https://www.autostudio.ru/catalog/ustanovka-avtosignalizaciy/starline/e66_v2_bt_eco_2can4lin.html',
            'https://www.autostudio.ru/catalog/ustanovka-avtosignalizaciy/starline/e96_v2_bt_2can4lin.html',
            'https://www.autostudio.ru/catalog/ustanovka-avtosignalizaciy/starline/starline_e96_v2_bt.html',
            'https://www.autostudio.ru/catalog/ustanovka-avtosignalizaciy/starline/s66_v2_eco.html',
            'https://www.autostudio.ru/catalog/ustanovka-avtosignalizaciy/starline/s66-bt-gsm.html',
            'https://www.autostudio.ru/catalog/ustanovka-avtosignalizaciy/starline/s66_v2_bt_lte.html',
            'https://www.autostudio.ru/catalog/ustanovka-avtosignalizaciy/starline/s96_v2_bt_gsm.html',
            'https://www.autostudio.ru/catalog/ustanovka-avtosignalizaciy/starline/s96_v2_bt_lte.html',
            'https://www.autostudio.ru/catalog/ustanovka-avtosignalizaciy/starline/s96_v2_bt_gsm_gps.html',
            'https://www.autostudio.ru/catalog/ustanovka-avtosignalizaciy/starline/s96_v2_bt_lte_gps.html',
            'https://www.autostudio.ru/catalog/ustanovka-avtosignalizaciy/starline/starline_s97_can_fd_gps.html',
            'https://www.autostudio.ru/catalog/ustanovka-avtosignalizaciy/starline/s97_fd_lte_gps.html',
            'https://www.autostudio.ru/catalog/ustanovka-avtosignalizaciy/starline/b97-v2-lte-gps.html'
        ];

        foreach ($urls as $url) {
            $this->info("Обработка: {$url}");

            $response = Http::get($url);

            if (!$response->successful()) {
                $this->error("Ошибка загрузки ({$response->status()}): {$url}");
                continue;
            }

            try {
                $this->parse($response->body());
            } catch (\Throwable $e) {
                $this->error("Ошибка парсинга: {$url} — {$e->getMessage()}");
            }
        }

        $this->info('Импорт завершён');
        return CommandAlias::SUCCESS;
    }

    private function parse(string $html): void
    {
        $crawler = new Crawler($html);

        /** H1 */
        $h1Node = $crawler->filter('h1');
        if ($h1Node->count() === 0) {
            return;
        }

        $h1 = trim($h1Node->first()->text() ?? '');
        if ($h1 === '') {
            return;
        }

        if (Product::query()->where('name', $h1)->exists()) {
            return;
        }

        /** Цена */
        $priceNode = $crawler->filter('meta[itemprop="price"]');
        if ($priceNode->count() === 0) {
            return;
        }

        $priceRaw = (string) $priceNode->first()->attr('content');
        $price    = (int) preg_replace('/\D+/', '', $priceRaw);

        if ($price <= 0) {
            return;
        }

        /** Описание */
        $descriptionNode = $crawler->filter('div.tab-pane.text');
        $description = $descriptionNode->count()
            ? trim($descriptionNode->first()->html())
            : null;

        /** Главная картинка */
        $mainImageUrl = null;
        $imgNode = $crawler->filter('img.img.right');
        if ($imgNode->count() > 0) {
            $src = trim($imgNode->first()->attr('src') ?? '');
            if ($src !== '') {
                $mainImageUrl = 'https://www.autostudio.ru' . $src;
            }
        }

        /** Таблица характеристик */
        $attributesData = [];

        $crawler->filter('table.params tr')->each(function (Crawler $tr) use (&$attributesData) {
            $tds = $tr->filter('td');

            if ($tds->count() !== 2) {
                return;
            }

            $attribute = trim($tds->eq(0)->text() ?? '');
            $value     = trim($tds->eq(1)->text() ?? '');

            if ($attribute === '' || $value === '') {
                return;
            }

            $attributesData[] = [
                'attribute' => Str::ucfirst($attribute),
                'value'     => $value,
            ];
        });

        /** Галерея */
        $galleryUrls = [];
        $crawler->filter('.foto a.thumbnail')->each(function (Crawler $a) use (&$galleryUrls) {
            $href = $a->attr('href');

            if ($href) {
                $galleryUrls[] = 'https://www.autostudio.ru' . $href;
            }
        });

        /** Создание товара */
        $product = new Product();
        $product->name = $h1;
        $product->description = $description;
        $product->save();

        // slug через трейт HasSlug
        $product->setSlug(Str::slug($h1));

        // Категория
        $product->categories()->attach(1, ['is_main' => true]);

        // Цены
        $product->prices()->create([
            'type'  => 2,
            'price' => $price,
        ]);

        $product->prices()->create([
            'type'  => 3,
            'price' => max(0, $price - 5000),
        ]);

        // Главная картинка
        if ($mainImageUrl) {
            $mainFilename = $this->downloadImage($mainImageUrl);

            if ($mainFilename) {
                $product->image = 'images/products/' . $mainFilename;
                $product->save();
            }
        }

        /** Атрибуты */
        if (!empty($attributesData)) {
            $attributeValueIds = [];

            foreach ($attributesData as $row) {
                $attribute = Attribute::query()
                    ->where('name', $row['attribute'])
                    ->first();

                if (!$attribute) {
                    continue;
                }

                $attributeValue = AttributeValue::query()
                    ->firstOrCreate(['value' => $row['value'], 'attribute_id' => $attribute->id]);

                $attributeValueIds[] = $attributeValue->id;

            }

            if (!empty($attributeValueIds)) {
                $product->attributeValues()->syncWithoutDetaching($attributeValueIds);
            }
        }

        /** Галерея изображений */
        if (!empty($galleryUrls)) {
            $imagesData = [];

            foreach ($galleryUrls as $url) {
                $filename = $this->downloadImage($url);

                if ($filename) {
                    $imagesData[] = [
                        'url' => 'images/products/' . $filename,
                    ];
                }
            }

            if (!empty($imagesData)) {
                $product->images()->createMany($imagesData);
            }
        }

        /** Производитель */
        $brandNode = $crawler->filter('meta[itemprop="manufacturer"]');

        if ($brandNode->count()) {
            $brand = trim((string) $brandNode->first()->attr('content'));

            if ($brand !== '') {
                $brandValue = AttributeValue::query()->firstOrCreate([
                    'attribute_id' => 58,
                    'value'        => $brand,
                ]);

                $product->attributeValues()->syncWithoutDetaching($brandValue->id);
            }
        }
    }

    private function downloadImage(string $url): ?string
    {
        $response = Http::get($url);

        if (!$response->successful()) {
            $this->error("Не удалось скачать изображение: {$url}");
            return null;
        }

        $path = parse_url($url, PHP_URL_PATH);
        $ext  = pathinfo($path, PATHINFO_EXTENSION) ?: 'jpg';

        $filename = now()->format('Y-m-d') . '-' . uniqid() . '.' . $ext;

        Storage::disk('public')->put("images/products/{$filename}", $response->body());

        return $filename;
    }
}
