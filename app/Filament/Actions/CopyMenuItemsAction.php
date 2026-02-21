<?php

namespace App\Filament\Actions;

use App\Models\Menu;
use App\Models\MenuItem;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class CopyMenuItemsAction
{
    /**
     * Действие для копирования пунктов меню из одного меню в другое.
     * Можно передать $sourceMenuId, чтобы зафиксировать источник (в RelationManager).
     */
    public static function make(?int $sourceMenuId = null): Action
    {
        return Action::make('copyMenuItems')
            ->label('Скопировать пункты меню')
            ->icon('heroicon-o-document-duplicate')
            ->color('gray')
            ->modalHeading('Копирование пунктов меню')
            ->modalDescription('Выберите откуда и куда скопировать пункты меню')
            ->modalSubmitActionLabel('Скопировать')
            ->schema(function () use ($sourceMenuId): array {
                $menuOptions = Menu::query()
                    ->orderBy('name')
                    ->pluck('name', 'id')
                    ->all();
                $components = [];
                if ($sourceMenuId === null) {
                    $components[] = Select::make('source_menu_id')
                        ->label('Меню-источник')
                        ->options($menuOptions)
                        ->required()
                        ->live()
                        ->helperText('Пункты из какого меню скопировать');
                }
                $components[] = Select::make('target_menu_id')
                    ->label('Целевое меню')
                    ->options(function () use ($sourceMenuId, $menuOptions) {
                        if ($sourceMenuId !== null) {
                            return collect($menuOptions)
                                ->except($sourceMenuId)
                                ->all();
                        }

                        return $menuOptions;
                    })
                    ->required()
                    ->helperText('В какое меню скопировать пункты');
                $components[] = Toggle::make('clear_target')
                    ->label('Очистить целевое меню перед копированием')
                    ->helperText('Все существующие пункты целевого меню будут удалены')
                    ->default(false);

                return $components;
            })
            ->action(function (array $data) use ($sourceMenuId): void {
                $fromMenuId = $sourceMenuId ?? (int) $data['source_menu_id'];
                $toMenuId = (int) $data['target_menu_id'];
                if ($fromMenuId === $toMenuId) {
                    Notification::make()
                        ->title('Ошибка')
                        ->body('Нельзя скопировать меню в самого себя')
                        ->danger()
                        ->send();

                    return;
                }
                DB::transaction(function () use ($fromMenuId, $toMenuId, $data): void {
                    if ($data['clear_target'] ?? false) {
                        MenuItem::query()->where('menu_id', $toMenuId)->delete();
                    }
                    $rootItems = MenuItem::query()
                        ->where('menu_id', $fromMenuId)
                        ->whereNull('parent_id')
                        ->orderBy('sort')
                        ->with('childrenRecursive')
                        ->get();
                    self::copyItemsRecursive($rootItems, $toMenuId, null);
                });
                Notification::make()
                    ->title('Готово')
                    ->body('Пункты меню успешно скопированы')
                    ->success()
                    ->send();
            });
    }

    /**
     * Рекурсивное копирование пунктов меню с сохранением иерархии.
     *
     * @param  \Illuminate\Database\Eloquent\Collection<int, MenuItem>  $items
     */
    protected static function copyItemsRecursive(
        \Illuminate\Database\Eloquent\Collection $items,
        int $targetMenuId,
        ?int $newParentId
    ): void {
        foreach ($items as $item) {
            $newItem = MenuItem::query()->create([
                'menu_id' => $targetMenuId,
                'parent_id' => $newParentId,
                'label' => $item->label,
                'type' => $item->type,
                'path' => $item->path,
                'url' => $item->url,
                'icon' => $item->icon,
                'sort' => $item->sort,
                'is_active' => $item->is_active,
                'open_in_new_tab' => $item->open_in_new_tab,
            ]);
            if ($item->relationLoaded('childrenRecursive') && $item->childrenRecursive->isNotEmpty()) {
                self::copyItemsRecursive($item->childrenRecursive, $targetMenuId, $newItem->id);
            }
        }
    }
}
