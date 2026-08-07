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

    protected static ?string $navigationLabel = 'General Settings';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 15;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Setting Details')->schema([
                Forms\Components\TextInput::make('key')
                    ->label('Setting Key')
                    ->required()
                    ->disabled(fn ($record) => $record !== null)
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('label')
                    ->label('Display Name / Description')
                    ->required(),
                Forms\Components\Textarea::make('value')
                    ->label('Setting Value')
                    ->required()
                    ->rows(3),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('label')
                    ->label('Setting')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('key')
                    ->label('Key')
                    ->searchable()
                    ->sortable()
                    ->fontFamily('mono'),
                Tables\Columns\TextColumn::make('value')
                    ->label('Value')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSettings::route('/'),
            'create' => Pages\CreateSetting::route('/create'),
            'edit'   => Pages\EditSetting::route('/{record}/edit'),
        ];
    }
}
