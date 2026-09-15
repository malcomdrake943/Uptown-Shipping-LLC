<?php

namespace App\Filament\Resources\DeliveryOptionResource\Pages;

use App\Filament\Resources\DeliveryOptionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDeliveryOptions extends ListRecords
{
    protected static string $resource = DeliveryOptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
