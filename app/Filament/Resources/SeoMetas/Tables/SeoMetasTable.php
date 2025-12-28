<?php

namespace App\Filament\Resources\SeoMetas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SeoMetasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('metaable_type')
                    ->searchable(),
                TextColumn::make('metaable_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('meta_title')
                    ->searchable(),
                TextColumn::make('canonical')
                    ->searchable(),
                TextColumn::make('og_title')
                    ->searchable(),
                ImageColumn::make('og_image'),
                TextColumn::make('og_type')
                    ->searchable(),
                TextColumn::make('twitter_card')
                    ->searchable(),
                TextColumn::make('twitter_title')
                    ->searchable(),
                ImageColumn::make('twitter_image'),
                IconColumn::make('noindex')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
