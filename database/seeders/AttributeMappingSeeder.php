<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class AttributeMappingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Маппинг атрибутов для ProductPresenter
        $mappings = [
            'brand' => 58,
            'gps' => 56,
            'gsm' => 55,
            'auto_start' => 31,
        ];

        Setting::set('product_attribute_mapping', null, $mappings);
    }
}
