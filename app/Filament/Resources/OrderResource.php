<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use App\Models\User;
use App\Notifications\OrderPurchasedNotification;
use App\Notifications\OrderShippedNotification;
use App\Notifications\OrderCancelledNotification;
use App\Notifications\OrderDeliveredNotification;
use App\Services\FeeCalculatorService;
use App\Services\ReconcilePaymentService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationLabel = 'Orders';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Product Information')->schema([
                Forms\Components\TextInput::make('order_number')->disabled(),
                Forms\Components\TextInput::make('product_url')->url()->disabled()->columnSpan(2),
                Forms\Components\TextInput::make('product_name')->disabled(),
                Forms\Components\Select::make('platform_id')
                    ->relationship('platform', 'name')
                    ->label('Selected Platform')
                    ->disabled(),
                Forms\Components\Select::make('source_platform')
                    ->options(['amazon' => 'Amazon', 'ebay' => 'eBay', 'other' => 'Other'])
                    ->disabled(),
                Forms\Components\TextInput::make('quantity')->numeric()->disabled(),
                Forms\Components\Select::make('size_tier')
                    ->options(['small' => 'Small', 'medium' => 'Medium', 'large' => 'Large', 'oversized' => 'Oversized'])
                    ->disabled(),
            ])->columns(2),

            Forms\Components\Section::make('Pricing')->schema([
                Forms\Components\TextInput::make('estimated_product_price')->prefix('$')->disabled(),
                Forms\Components\TextInput::make('final_product_price')->prefix('$')->nullable()->disabled(),
                Forms\Components\TextInput::make('service_fee')->prefix('$')->disabled(),
                Forms\Components\TextInput::make('size_handling_fee')->prefix('$')->disabled(),
                Forms\Components\TextInput::make('total_charged')->prefix('$')->disabled(),
                Forms\Components\Select::make('price_reconciliation_status')
                    ->options([
                        'none'                   => 'None',
                        'refund_due'             => 'Refund Due',
                        'additional_payment_due' => 'Additional Payment Due',
                        'resolved'               => 'Resolved',
                    ])->disabled(),
            ])->columns(2),

            Forms\Components\Section::make('Customer Information')->schema([
                Forms\Components\TextInput::make('customer_name')->disabled(),
                Forms\Components\TextInput::make('customer_email')->email()->disabled(),
                Forms\Components\TextInput::make('customer_phone')->disabled(),
                Forms\Components\Textarea::make('customer_notes')->disabled()->columnSpan(2),
            ])->columns(2),

            Forms\Components\Section::make('Status & Tracking')->schema([
                Forms\Components\Select::make('status')
                    ->options([
                        'pending'      => 'Pending',
                        'under_review' => 'Under Review',
                        'purchased'    => 'Purchased',
                        'shipped'      => 'Shipped',
                        'delivered'    => 'Delivered',
                    ]),
                Forms\Components\Select::make('handled_by')
                    ->relationship('handler', 'name')
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('tracking_carrier'),
                Forms\Components\TextInput::make('tracking_number'),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->color(fn (Order $r) => $r->status === 'under_review' ? 'warning' : null),
                Tables\Columns\TextColumn::make('customer_name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('customer_email')->searchable()->toggleable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'gray'    => 'pending',
                        'warning' => 'under_review',
                        'primary' => 'purchased',
                        'info'    => 'shipped',
                        'success' => 'delivered',
                        'danger'  => ['cancelled', 'refunded'],
                    ]),
                Tables\Columns\TextColumn::make('platform.name')
                    ->label('Platform')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('source_platform')
                    ->colors(['primary' => 'amazon', 'warning' => 'ebay', 'gray' => 'other']),
                Tables\Columns\BadgeColumn::make('size_tier'),
                Tables\Columns\TextColumn::make('total_charged')
                    ->money('usd')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending'      => 'Pending',
                        'under_review' => 'Under Review (Manual Quote)',
                        'purchased'    => 'Purchased',
                        'shipped'      => 'Shipped',
                        'delivered'    => 'Delivered',
                    ]),
                Tables\Filters\SelectFilter::make('platform')
                    ->label('Website')
                    ->relationship('platform', 'name'),
                Tables\Filters\SelectFilter::make('source_platform')
                    ->options(['amazon' => 'Amazon', 'ebay' => 'eBay', 'other' => 'Other']),
                Tables\Filters\SelectFilter::make('size_tier')
                    ->options(['small' => 'Small', 'medium' => 'Medium', 'large' => 'Large', 'oversized' => 'Oversized']),
                Tables\Filters\Filter::make('country')
                    ->form([
                        Forms\Components\TextInput::make('country')->label('Shipping Country'),
                    ])
                    ->query(fn (Builder $query, array $data) => $query->when(
                        $data['country'],
                        fn ($q) => $q->whereJsonContains('shipping_address->country', $data['country'])
                    )),
            ])
            ->actions([
                // Mark Under Review
                Tables\Actions\Action::make('mark_under_review')
                    ->label('Under Review')
                    ->icon('heroicon-o-eye')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->visible(fn (Order $r) => $r->status === 'pending')
                    ->action(function (Order $record) {
                        $record->recordStatusChange('under_review', 'Moved to under review', auth()->id());
                        Notification::make()->title('Order marked as Under Review')->success()->send();
                    }),


                // Mark Purchased
                Tables\Actions\Action::make('mark_purchased')
                    ->label('Mark Purchased')
                    ->icon('heroicon-o-check-circle')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->visible(fn (Order $r) => in_array($r->status, ['pending', 'under_review']))
                    ->action(function (Order $record) {
                        $record->recordStatusChange('purchased', 'Item purchased by staff', auth()->id());
                        $record->update(['handled_by' => auth()->id()]);
                        $record->notify(new OrderPurchasedNotification($record));
                        Notification::make()->title('Order marked as Purchased. Customer notified.')->success()->send();
                    }),

                // Add Tracking Info
                Tables\Actions\Action::make('add_tracking')
                    ->label('Add Tracking')
                    ->icon('heroicon-o-truck')
                    ->color('info')
                    ->visible(fn (Order $r) => $r->status === 'purchased')
                    ->form([
                        Forms\Components\TextInput::make('tracking_carrier')
                            ->label('Carrier (e.g. FedEx, DHL)')
                            ->required(),
                        Forms\Components\TextInput::make('tracking_number')
                            ->required(),
                    ])
                    ->action(function (Order $record, array $data) {
                        $record->update([
                            'tracking_carrier' => $data['tracking_carrier'],
                            'tracking_number'  => $data['tracking_number'],
                        ]);
                        $record->recordStatusChange('shipped', "Tracking: {$data['tracking_carrier']} {$data['tracking_number']}", auth()->id());
                        $record->notify(new OrderShippedNotification($record));
                        Notification::make()->title('Tracking info saved. Customer notified.')->success()->send();
                    }),

                // Mark Delivered
                Tables\Actions\Action::make('mark_delivered')
                    ->label('Mark Delivered')
                    ->icon('heroicon-o-home')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Order $r) => $r->status === 'shipped')
                    ->action(function (Order $record) {
                        $record->recordStatusChange('delivered', 'Marked delivered by staff', auth()->id());
                        $record->notify(new OrderDeliveredNotification($record));
                        Notification::make()->title('Order marked as Delivered.')->success()->send();
                    }),


                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->defaultSort('created_at', 'asc')
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view'   => Pages\ViewOrder::route('/{record}'),
            'edit'   => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::whereIn('status', ['pending', 'under_review'])->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return static::getModel()::where('status', 'under_review')->exists() ? 'warning' : 'primary';
    }
}
