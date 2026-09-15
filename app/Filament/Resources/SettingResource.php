<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'All Site Settings';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 15;


    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Setting Information')->schema([
                Forms\Components\TextInput::make('key')
                    ->label('Setting Key')
                    ->disabled()
                    ->required(),
                Forms\Components\TextInput::make('label')
                    ->label('Setting Name / Description')
                    ->required(),
                Forms\Components\Select::make('group')
                    ->label('Group')
                    ->options([
                        'general'      => 'General Settings',
                        'payment'      => 'Payment Settings',
                        'home_page'    => 'Main Page Section',
                        'about_page'   => 'About Us Page',
                        'contact_page' => 'Contact Us Page',
                    ])
                    ->default('general')
                    ->required(),

                // Dynamic Value Input based on key type
                Forms\Components\Select::make('value')
                    ->label('Setting Value (Visibility)')
                    ->options([
                        'true'  => 'Visible (Show on site)',
                        'false' => 'Hidden (Hide from site)',
                    ])
                    ->visible(fn ($record) => $record && $record->key === 'home_about_visible')
                    ->required(fn ($record) => $record && $record->key === 'home_about_visible'),

                Forms\Components\Textarea::make('value')
                    ->label('Setting Content / Value')
                    ->rows(6)
                    ->helperText('Multi-line content or description.')
                    ->visible(fn ($record) => ! $record || (
                        $record->key !== 'home_about_visible' &&
                        (str_contains($record->key, 'content') ||
                         str_contains($record->key, 'mission') ||
                         str_contains($record->key, 'vision') ||
                         str_contains($record->key, 'subtitle') ||
                         str_contains($record->key, 'description') ||
                         str_contains($record->key, 'address') ||
                         str_contains($record->key, 'hours') ||
                         str_contains($record->key, 'shipping') ||
                         str_contains($record->key, 'intro'))
                    ))
                    ->required(fn ($record) => ! $record || $record->key !== 'home_about_visible'),

                Forms\Components\TextInput::make('value')
                    ->label('Setting Value')
                    ->visible(fn ($record) => $record &&
                        $record->key !== 'home_about_visible' &&
                        ! str_contains($record->key, 'content') &&
                        ! str_contains($record->key, 'mission') &&
                        ! str_contains($record->key, 'vision') &&
                        ! str_contains($record->key, 'subtitle') &&
                        ! str_contains($record->key, 'description') &&
                        ! str_contains($record->key, 'address') &&
                        ! str_contains($record->key, 'hours') &&
                        ! str_contains($record->key, 'shipping') &&
                        ! str_contains($record->key, 'intro')
                    )
                    ->required(fn ($record) => $record && $record->key !== 'home_about_visible'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('label')
                    ->label('Setting Name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('group')
                    ->label('Group')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'payment'      => 'warning',
                        'home_page'    => 'success',
                        'about_page'   => 'info',
                        'contact_page' => 'primary',
                        default        => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('key')
                    ->label('Key')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('value')
                    ->label('Current Value')
                    ->limit(60)
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('group')
                    ->options([
                        'general'      => 'General Settings',
                        'payment'      => 'Payment Settings',
                        'home_page'    => 'Main Page Section',
                        'about_page'   => 'About Us Page',
                        'contact_page' => 'Contact Us Page',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSettings::route('/'),
            'create' => Pages\CreateSetting::route('/create'),
            'edit' => Pages\EditSetting::route('/{record}/edit'),
        ];
    }
}

