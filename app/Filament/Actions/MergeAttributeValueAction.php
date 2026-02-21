<?php

namespace App\Filament\Actions;

use App\Models\AttributeValue;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\DB;

class MergeAttributeValueAction
{
    /**
     * Действие для переноса всех товаров с одного значения атрибута на другое и удаления дубля.
     */
    public static function make(): Action
    {
        return Action::make('mergeAttributeValue')
            ->label('Перенести и удалить')
            ->icon(Heroicon::OutlinedArrowsRightLeft)
            ->color('warning')
            ->modalHeading('Перенос значения атрибута')
            ->modalDescription('Все товары с текущим значением будут переназначены на выбранное, после чего текущее значение будет удалено.')
            ->modalSubmitActionLabel('Перенести и удалить')
            ->schema(fn (AttributeValue $record): array => [
                Select::make('target_attribute_value_id')
                    ->label('Перенести в')
                    ->helperText("Текущее значение: «{$record->value}» (атрибут: {$record->attribute->name})")
                    ->options(
                        AttributeValue::query()
                            ->where('attribute_id', $record->attribute_id)
                            ->where('id', '!=', $record->id)
                            ->orderBy('value')
                            ->pluck('value', 'id')
                            ->toArray()
                    )
                    ->required()
                    ->searchable()
                    ->native(false),
            ])
            ->action(function (AttributeValue $record, array $data): void {
                $targetId = (int) $data['target_attribute_value_id'];

                DB::transaction(function () use ($record, $targetId): void {
                    // Получаем товары, у которых уже есть целевое значение — их нельзя перенести (дубль в pivot)
                    $alreadyHasTarget = DB::table('product_attributes')
                        ->where('attribute_value_id', $targetId)
                        ->pluck('product_id')
                        ->toArray();

                    // Переносим только те товары, у которых ещё нет целевого значения
                    DB::table('product_attributes')
                        ->where('attribute_value_id', $record->id)
                        ->whereNotIn('product_id', $alreadyHasTarget)
                        ->update(['attribute_value_id' => $targetId]);

                    // Удаляем оставшиеся (дубли — товары, у которых уже было целевое значение)
                    DB::table('product_attributes')
                        ->where('attribute_value_id', $record->id)
                        ->delete();

                    $record->delete();
                });

                Notification::make()
                    ->title('Готово')
                    ->body('Значение перенесено и удалено.')
                    ->success()
                    ->send();
            });
    }
}
