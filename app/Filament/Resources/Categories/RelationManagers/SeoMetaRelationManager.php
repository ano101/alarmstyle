<?php

namespace App\Filament\Resources\Categories\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SeoMetaRelationManager extends RelationManager
{
    protected static string $relationship = 'seoMeta';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('meta_title'),
                Textarea::make('meta_description')
                    ->columnSpanFull(),
                Textarea::make('meta_keywords')
                    ->columnSpanFull(),
                TextInput::make('canonical'),
                TextInput::make('og_title'),
                Textarea::make('og_description')
                    ->columnSpanFull(),
                FileUpload::make('og_image')
                    ->image(),
                TextInput::make('og_type')
                    ->default('website'),
                TextInput::make('twitter_card')
                    ->default('summary_large_image'),
                TextInput::make('twitter_title'),
                Textarea::make('twitter_description')
                    ->columnSpanFull(),
                FileUpload::make('twitter_image')
                    ->image(),
                Toggle::make('noindex')
                    ->required(),
                TextInput::make('extra'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
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
