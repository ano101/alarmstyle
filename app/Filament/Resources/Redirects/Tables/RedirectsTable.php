<?php

namespace App\Filament\Resources\Redirects\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class RedirectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('from_url')
                    ->label('Откуда')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('URL скопирован')
                    ->tooltip('Кликните для копирования'),

                TextColumn::make('to_url')
                    ->label('Куда')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('URL скопирован')
                    ->tooltip('Кликните для копирования'),

                TextColumn::make('status_code')
                    ->label('Код')
                    ->badge()
                    ->color(fn (int $state): string => match ($state) {
                        301 => 'success',
                        302 => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (int $state): string => match ($state) {
                        301 => '301 (Постоянный)',
                        302 => '302 (Временный)',
                        default => (string) $state,
                    })
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Активен')
                    ->boolean()
                    ->trueIcon(Heroicon::OutlinedCheckCircle)
                    ->falseIcon(Heroicon::OutlinedXCircle)
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Создан')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Обновлён')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Статус')
                    ->placeholder('Все редиректы')
                    ->trueLabel('Только активные')
                    ->falseLabel('Только неактивные'),

                SelectFilter::make('status_code')
                    ->label('Код ответа')
                    ->options([
                        301 => '301 - Постоянный',
                        302 => '302 - Временный',
                    ]),
            ])
            ->deferFilters(false)
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
