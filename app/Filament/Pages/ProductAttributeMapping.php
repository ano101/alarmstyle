<?php

namespace App\Filament\Pages;

use App\Models\Attribute;
use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Form as FormComponent;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

/**
 * @property-read Schema $form
 */
class ProductAttributeMapping extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedAdjustmentsHorizontal;

    protected string $view = 'filament.pages.product-attribute-mapping';

    protected static ?string $navigationLabel = 'Маппинг атрибутов';

    protected static ?string $title = 'Маппинг атрибутов';

    protected static ?int $navigationSort = 10;

    public static function getNavigationGroup(): ?string
    {
        return 'Система';
    }

    public ?array $data = [];

    public function mount(): void
    {
        $mapping = Setting::getData('product_attribute_mapping', [
            'brand' => null,
            'gps' => null,
            'gsm' => null,
            'auto_start' => null,
        ]);

        $this->form->fill(['mapping' => $mapping]);
    }

    public function form(Schema $schema): Schema
    {
        $attributes = Attribute::query()
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();

        return $schema
            ->components([
                FormComponent::make([
                    Section::make('Маппинг атрибутов для ProductPresenter')
                        ->description('Настройте соответствие между ключами атрибутов и реальными атрибутами из БД.')
                        ->schema([
                            Forms\Components\Select::make('mapping.brand')
                                ->label('Бренд (brand)')
                                ->options($attributes)
                                ->searchable()
                                ->helperText('Атрибут для метода brand()'),

                            Forms\Components\Select::make('mapping.gps')
                                ->label('GPS (gps)')
                                ->options($attributes)
                                ->searchable()
                                ->helperText('Атрибут для метода gps()'),

                            Forms\Components\Select::make('mapping.gsm')
                                ->label('GSM (gsm)')
                                ->options($attributes)
                                ->searchable()
                                ->helperText('Атрибут для метода gsm()'),

                            Forms\Components\Select::make('mapping.auto_start')
                                ->label('Автозапуск (auto_start)')
                                ->options($attributes)
                                ->searchable()
                                ->helperText('Атрибут для метода autoStart()'),
                        ]),
                ])
                    ->livewireSubmitHandler('save')
                    ->footer([
                        \Filament\Schemas\Components\Actions::make([
                            Action::make('save')
                                ->label('Сохранить')
                                ->submit('save')
                                ->keyBindings(['mod+s']),
                        ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // Фильтруем null значения
        $mapping = array_filter($data['mapping'] ?? [], fn ($value) => $value !== null);

        Setting::set('product_attribute_mapping', null, $mapping);

        Notification::make()
            ->success()
            ->title('Настройки сохранены')
            ->send();
    }
}
