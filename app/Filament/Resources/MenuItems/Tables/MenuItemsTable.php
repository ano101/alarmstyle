<?php

namespace App\Filament\Resources\MenuItems\Tables;

use App\Filament\Actions\CopyMenuItemsAction;
use App\Models\Menu;
use App\Models\MenuItem;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MenuItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            // reorder лучше работает без пагинации
            ->paginated(false)

            // Drag & Drop сортировка по полю sort
            ->reorderable('sort')

            // дефолтная сортировка
            ->defaultSort('sort')

            ->columns([
                TextColumn::make('label')
                    ->label('Название')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    // неактивные можно приглушать визуально
                    ->color(fn (MenuItem $record) => $record->is_active ? null : 'gray'),

                TextColumn::make('menu.name')
                    ->label('Меню')
                    ->badge()
                    ->sortable(),

                TextColumn::make('parent.label')
                    ->label('Родитель')
                    ->toggleable()
                    ->sortable(),

                TextColumn::make('type')
                    ->label('Тип')
                    ->badge()
                    ->sortable(),

                // полезно видеть куда ведёт
                TextColumn::make('resolved_href')
                    ->label('Ссылка')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->limit(60),

                TextColumn::make('sort')
                    ->label('Sort')
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Активен')
                    ->boolean()
                    ->sortable(),
            ])

            ->filters([
                SelectFilter::make('menu_id')
                    ->label('Меню')
                    ->options(fn () => Menu::query()->orderBy('name')->pluck('name', 'id')->all())
                    // если хочешь — можно дефолтить на первый menu
                    ->default(fn () => Menu::query()->orderBy('id')->value('id'))
                    ->preload()
                    ->query(function (Builder $query, $data) {
                        if ($data['value']) {
                            $query->where('menu_id', $data['value']);
                        }
                    }),

                SelectFilter::make('parent_level')
                    ->label('Уровень (родитель)')
                    ->options(function (SelectFilter $filter) {
                        $menuId = $filter->getTable()->getFilter('menu_id')?->getState();

                        $roots = MenuItem::query()
                            ->when($menuId, fn (Builder $q) => $q->where('menu_id', $menuId))
                            ->whereNull('parent_id')
                            ->orderBy('label')
                            ->pluck('label', 'id')
                            ->all();

                        return ['root' => '— Корень —'] + $roots;
                    })
                    ->default('root')
                    ->preload()
                    ->query(function (Builder $query, $data) {
                        $value = $data['value'] ?? null;

                        if ($value === 'root') {
                            $query->whereNull('parent_id');
                        } elseif ($value) {
                            $query->where('parent_id', $value);
                        }
                    }),
            ])

            // ограничиваем список выбранным меню + уровнем,
            // чтобы drag&drop сортировал ТОЛЬКО внутри уровня
            ->toolbarActions([
                CopyMenuItemsAction::make(),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                // В Filament v4 фильтры автоматически применяются к запросу
                // через свои callback-функции в ->query()
                // Поэтому здесь можно оставить пустую функцию
                // или добавить дополнительную логику, если нужно
            });
    }
}
