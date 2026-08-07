<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    public function getTabs(): array
    {
        return [
            'all'          => Tab::make('All'),
            'pending'      => Tab::make('Pending')->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'pending')),
            'under_review' => Tab::make('Manual Quote')->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'under_review')),
            'purchased'    => Tab::make('Purchased')->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'purchased')),
            'shipped'      => Tab::make('Shipped')->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'shipped')),
            'delivered'    => Tab::make('Delivered')->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'delivered')),
        ];
    }
}
