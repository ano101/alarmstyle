<?php

namespace App\Filament\Resources\Settings\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;

class SettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('key')->label('Ключ')->searchable(),
                \Filament\Tables\Columns\TextColumn::make('value')
                    ->label('Значение')
                    ->limit(40),
                \Filament\Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('d.m.Y H:i')
                    ->label('Обновлено'),
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
