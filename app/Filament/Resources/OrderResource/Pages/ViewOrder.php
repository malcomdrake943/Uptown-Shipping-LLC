<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Components\Section::make('Order Summary')->schema([
                Components\TextEntry::make('order_number')->weight('bold'),
                Components\TextEntry::make('status')->badge(),
                Components\TextEntry::make('created_at')->dateTime(),
                Components\TextEntry::make('customer_name'),
                Components\TextEntry::make('customer_email'),
                Components\TextEntry::make('customer_phone'),
            ])->columns(3),

            Components\Section::make('Product')->schema([
                Components\TextEntry::make('product_url')->url(fn ($state) => $state)->openUrlInNewTab()->columnSpan(2),
                Components\TextEntry::make('product_name'),
                Components\TextEntry::make('source_platform')->badge(),
                Components\TextEntry::make('size_tier'),
                Components\TextEntry::make('quantity'),
            ])->columns(3),

            Components\Section::make('Pricing')->schema([
                Components\TextEntry::make('estimated_product_price')->money('usd'),
                Components\TextEntry::make('final_product_price')->money('usd'),
                Components\TextEntry::make('service_fee')->money('usd'),
                Components\TextEntry::make('size_handling_fee')->money('usd'),
                Components\TextEntry::make('total_charged')->money('usd')->weight('bold'),
                Components\TextEntry::make('price_reconciliation_status')->badge(),
            ])->columns(3),

            Components\Section::make('Shipping Address')->schema([
                Components\TextEntry::make('shipping_address')
                    ->formatStateUsing(function ($state) {
                        $addr = is_array($state) ? $state : (array) $state;
                        return implode(', ', array_filter([
                            $addr['line1'] ?? '',
                            $addr['line2'] ?? '',
                            $addr['city'] ?? '',
                            $addr['state'] ?? '',
                            $addr['postal_code'] ?? '',
                            $addr['country'] ?? '',
                        ]));
                    })
                    ->columnSpan(3),
            ])->columns(3),

            Components\Section::make('Tracking')->schema([
                Components\TextEntry::make('tracking_carrier'),
                Components\TextEntry::make('tracking_number'),
            ])->columns(2),

            Components\Section::make('Notes')->schema([
                Components\TextEntry::make('customer_notes')->columnSpan(2),
            ]),
        ]);
    }
}
