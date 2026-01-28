<?php

namespace App\Filament\Resources\CategoryLandings\RelationManagers;

use App\Models\CatalogQuickLink;
use App\Models\CategoryLanding;
use App\Services\CatalogService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CatalogQuickLinkRelationManager extends RelationManager
{
    protected static string $relationship = 'quickLinks';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('label')
                ->label('Название')
                ->required()
                ->maxLength(100),

            Select::make('type')
                ->label('Тип')
                ->required()
                ->options([
                    'catalog_path' => 'Каталог: фильтр (path)',
                    'catalog_landing' => 'Каталог: landing',
                    'custom_url' => 'Ссылка: внешний URL',
                ])
                ->reactive()
                ->rule(function () {
                    return function (string $attribute, $value, $fail) {
                        // catalog_landing разрешаем только если владелец — CategoryLanding
                        if ($value === 'catalog_landing' && ! ($this->getOwnerRecord() instanceof CategoryLanding)) {
                            $fail('Тип "Каталог: landing" доступен только для посадочных страниц (Landing).');
                        }
                    };
                }),

            TextInput::make('path')
                ->label('Path')
                ->helperText('Например: alarms/pandora/gsm (без ведущего /)')
                ->visible(fn ($get) => $get('type') === 'catalog_path')
                ->required(fn ($get) => $get('type') === 'catalog_path')
                ->maxLength(255)
                // базовая валидация формата
                ->regex('/^[a-z0-9]+(?:-[a-z0-9]+)*(?:\/[a-z0-9]+(?:-[a-z0-9]+)*)*$/')
                // ВАЖНО: проверка, что path реально резолвится (не 404)
                ->rule(function () {
                    return function (string $attribute, $value, $fail) {
                        if (! $value) {
                            return;
                        }

                        /** @var CatalogService $catalog */
                        $catalog = app(CatalogService::class);

                        try {
                            // имитируем запрос на резолв (твой resolvePath бросает NotFoundHttpException)
                            $catalog->resolvePath(trim((string) $value, '/'));
                        } catch (NotFoundHttpException $e) {
                            $fail('Path ведёт на 404. Проверь категорию/slug-значения фильтра.');
                        } catch (\Throwable $e) {
                            // если вдруг что-то неожиданное
                            $fail('Не удалось проверить path. Проверь значение.');
                        }
                    };
                }),

            TextInput::make('url')
                ->label('URL')
                ->helperText('Абсолютный URL')
                ->visible(fn ($get) => $get('type') === 'custom_url')
                ->required(fn ($get) => $get('type') === 'custom_url')
                ->url()
                ->maxLength(2048),

            TextInput::make('icon')
                ->label('Иконка')
                ->helperText('Например: heroicon-o-arrow-top-right-on-square')
                ->maxLength(80),

            TextInput::make('color')
                ->label('Цвет')
                ->helperText('primary | success | warning | danger | gray')
                ->maxLength(20),

            TextInput::make('sort')
                ->label('Сортировка')
                ->numeric()
                ->default(0),

            Toggle::make('is_active')
                ->label('Активна')
                ->default(true),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('label')
            ->defaultSort('sort')
            ->columns([
                TextColumn::make('label')
                    ->label('Кнопка')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type')
                    ->label('Тип')
                    ->badge()
                    ->sortable(),

                TextColumn::make('path')
                    ->label('Path')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->limit(40),

                TextColumn::make('url')
                    ->label('URL')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->limit(40),

                TextColumn::make('sort')
                    ->label('Sort')
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Активна')
                    ->boolean()
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                Action::make('open')
                    ->label('Открыть')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (CatalogQuickLink $record) => $record->resolveHref(), shouldOpenInNewTab: true),

                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
