<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SizeFeeRuleResource\Pages;
use App\Models\SizeFeeRule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SizeFeeRuleResource extends Resource
{
    protected static ?string $model = SizeFeeRule::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationLabel = 'Size Fee Rules';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 11;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('size_tier')
                ->options(['small' => 'Small', 'medium' => 'Medium', 'large' => 'Large', 'oversized' => 'Oversized'])
                ->required()
                ->unique(ignoreRecord: true),
            Forms\Components\TextInput::make('flat_fee')
                ->label('Flat Handling Fee ($)')
                ->numeric()
                ->required()
                ->prefix('$'),
            Forms\Components\Toggle::make('requires_manual_quote')
                ->label('Requires Manual Quote (skip payment collection)')
                ->helperText('When enabled, orders with this size tier will be placed under review and staff will quote manually.'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\BadgeColumn::make('size_tier')
                    ->colors([
                        'success' => 'small',
                        'primary' => 'medium',
                        'warning' => 'large',
                        'danger'  => 'oversized',
                    ]),
                Tables\Columns\TextColumn::make('flat_fee')->money('usd'),
                Tables\Columns\IconColumn::make('requires_manual_quote')->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSizeFeeRules::route('/'),
            'edit'  => Pages\EditSizeFeeRule::route('/{record}/edit'),
        ];
    }
}
