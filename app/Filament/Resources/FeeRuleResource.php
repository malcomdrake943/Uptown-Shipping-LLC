<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FeeRuleResource\Pages;
use App\Models\FeeRule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FeeRuleResource extends Resource
{
    protected static ?string $model = FeeRule::class;

    protected static ?string $navigationIcon = 'heroicon-o-percent-badge';

    protected static ?string $navigationLabel = 'Fee Rules';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('min_price')
                ->label('Min Price ($)')
                ->numeric()
                ->required()
                ->default(0),
            Forms\Components\TextInput::make('max_price')
                ->label('Max Price ($) — leave blank for unbounded')
                ->numeric()
                ->nullable(),
            Forms\Components\Select::make('fee_type')
                ->options(['flat' => 'Flat ($)', 'percentage' => 'Percentage (%)'])
                ->required(),
            Forms\Components\TextInput::make('fee_value')
                ->label('Fee Value ($ or %)')
                ->numeric()
                ->required(),
            Forms\Components\TextInput::make('sort_order')
                ->numeric()
                ->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('min_price')
                    ->formatStateUsing(fn ($state) => $state !== null ? '$' . number_format((float) $state, 2) : '$0.00')
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_price')
                    ->formatStateUsing(fn ($state) => $state !== null ? '$' . number_format((float) $state, 2) : 'Unbounded')
                    ->default('Unbounded'),
                Tables\Columns\BadgeColumn::make('fee_type')
                    ->colors(['primary' => 'percentage', 'success' => 'flat']),
                Tables\Columns\TextColumn::make('fee_value')
                    ->formatStateUsing(fn ($state, FeeRule $r) => $r->fee_type === 'percentage' ? "{$state}%" : "$" . number_format($state, 2)),
                Tables\Columns\TextColumn::make('sort_order')->sortable(),
            ])
            ->defaultSort('sort_order')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->reorderable('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListFeeRules::route('/'),
            'create' => Pages\CreateFeeRule::route('/create'),
            'edit'   => Pages\EditFeeRule::route('/{record}/edit'),
        ];
    }
}
