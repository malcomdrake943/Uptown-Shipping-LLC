<?php

namespace App\Filament\Resources\DeliveryOptionResource\Pages;

use App\Filament\Resources\DeliveryOptionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDeliveryOption extends EditRecord
{
    protected static string $resource = DeliveryOptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
