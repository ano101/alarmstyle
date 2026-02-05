<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class MenuItemsSeeder extends Seeder
{
    public function run(): void
    {
        // Создаем мобильное меню если его нет
        $mobileMenu = Menu::firstOrCreate(
            ['key' => 'mob_menu'],
            [
                'name' => 'Мобильное меню',
                'is_active' => true,
            ]
        );

        $headerMenu = Menu::where('key', 'header')->first();

        if ($headerMenu) {
            // Добавляем элементы в главное меню (header)
            $this->addHeaderMenuItems($headerMenu);
        }

        // Добавляем элементы в мобильное меню (без главной страницы)
        $this->addMobileMenuItems($mobileMenu);
    }

    private function addHeaderMenuItems(Menu $menu): void
    {
        // Главная
        MenuItem::firstOrCreate(
            [
                'menu_id' => $menu->id,
                'type' => 'internal_path',
                'path' => '',
                'label' => 'Главная',
            ],
            [
                'parent_id' => null,
                'url' => null,
                'icon' => null,
                'sort' => 0,
                'is_active' => true,
                'open_in_new_tab' => false,
            ]
        );

        // О компании
        MenuItem::firstOrCreate(
            [
                'menu_id' => $menu->id,
                'type' => 'internal_path',
                'path' => 'o-kompanii',
                'label' => 'О компании',
            ],
            [
                'parent_id' => null,
                'url' => null,
                'icon' => null,
                'sort' => 100,
                'is_active' => true,
                'open_in_new_tab' => false,
            ]
        );

        // Контакты
        MenuItem::firstOrCreate(
            [
                'menu_id' => $menu->id,
                'type' => 'internal_path',
                'path' => 'kontakty',
                'label' => 'Контакты',
            ],
            [
                'parent_id' => null,
                'url' => null,
                'icon' => null,
                'sort' => 101,
                'is_active' => true,
                'open_in_new_tab' => false,
            ]
        );
    }

    private function addMobileMenuItems(Menu $menu): void
    {
        // Каталог (группа с подкатегориями)
        $catalogItem = MenuItem::firstOrCreate(
            [
                'menu_id' => $menu->id,
                'type' => 'group',
                'label' => 'Каталог',
            ],
            [
                'parent_id' => null,
                'path' => null,
                'url' => null,
                'icon' => null,
                'sort' => 0,
                'is_active' => true,
                'open_in_new_tab' => false,
            ]
        );

        // Автосигнализации (подкатегория каталога)
        MenuItem::firstOrCreate(
            [
                'menu_id' => $menu->id,
                'parent_id' => $catalogItem->id,
                'type' => 'internal_path',
                'path' => 'category/avtosignalizacii',
            ],
            [
                'label' => 'Автосигнализации',
                'url' => null,
                'icon' => null,
                'sort' => 0,
                'is_active' => true,
                'open_in_new_tab' => false,
            ]
        );

        // О компании
        MenuItem::firstOrCreate(
            [
                'menu_id' => $menu->id,
                'type' => 'internal_path',
                'path' => 'o-kompanii',
                'parent_id' => null,
            ],
            [
                'label' => 'О компании',
                'url' => null,
                'icon' => null,
                'sort' => 1,
                'is_active' => true,
                'open_in_new_tab' => false,
            ]
        );

        // Контакты
        MenuItem::firstOrCreate(
            [
                'menu_id' => $menu->id,
                'type' => 'internal_path',
                'path' => 'kontakty',
                'parent_id' => null,
            ],
            [
                'label' => 'Контакты',
                'url' => null,
                'icon' => null,
                'sort' => 2,
                'is_active' => true,
                'open_in_new_tab' => false,
            ]
        );
    }
}
