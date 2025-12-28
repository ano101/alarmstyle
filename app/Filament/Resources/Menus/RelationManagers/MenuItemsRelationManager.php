<?php

namespace App\Filament\Resources\Menus\RelationManagers;

use App\Models\MenuItem;
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
use Filament\Tables;
use Filament\Tables\Table;

class MenuItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'Пункты меню';

    protected static ?string $recordTitleAttribute = 'label';

    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('label')
                ->label('Название')
                ->required()
                ->maxLength(120),

            Select::make('type')
                ->label('Тип ссылки')
                ->required()
                ->options([
                    'group'         => 'Вкладка (без ссылки)',
                    'internal_path' => 'Внутренняя (path)',
                    'external_url'  => 'Внешняя (URL)',
                ])
                ->reactive(),

            TextInput::make('path')
                ->label('Path')
                ->visible(fn ($get) => $get('type') === 'internal_path')
                ->required(fn ($get) => $get('type') === 'internal_path')
                ->maxLength(2048),

            TextInput::make('url')
                ->label('URL')
                ->visible(fn ($get) => $get('type') === 'external_url')
                ->required(fn ($get) => $get('type') === 'external_url')
                ->url()
                ->maxLength(2048),

            // Родитель только в рамках текущего меню
            Select::make('parent_id')
                ->label('Родитель')
                ->options(fn () => $this->getOwnerRecord()
                    ? $this->getOwnerRecord()
                        ->items()
                        ->orderBy('sort')
                        ->pluck('label', 'id')
                    : MenuItem::query()
                        ->orderBy('sort')
                        ->pluck('label', 'id')
                )
                ->searchable()
                ->nullable()
                ->helperText('Только пункты этого же меню'),

            TextInput::make('icon')
                ->label('Иконка')
                ->maxLength(80)
                ->helperText('Например: heroicon-o-home'),

            TextInput::make('sort')
                ->label('Сортировка')
                ->numeric()
                ->default(0),

            Toggle::make('is_active')
                ->label('Активен')
                ->default(true),

            Toggle::make('open_in_new_tab')
                ->label('Открывать в новой вкладке')
                ->default(false),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('label')
            ->columns([
                Tables\Columns\TextColumn::make('label')
                    ->label('Название')
                    ->searchable(),

                Tables\Columns\TextColumn::make('parent.label')
                    ->label('Родитель')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Тип')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'group'         => 'Группа',
                        'internal_path' => 'Внутренняя',
                        'external_url'  => 'Внешняя',
                        default         => $state,
                    }),

                Tables\Columns\TextColumn::make('resolved_href')
                    ->label('Ссылка')
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->resolved_href),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Активен')
                    ->boolean(),

                Tables\Columns\TextColumn::make('sort')
                    ->label('Сорт.')
                    ->sortable(),
            ])
            ->defaultSort('sort')
            ->reorderable('sort') // drag & drop сортировка
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
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
