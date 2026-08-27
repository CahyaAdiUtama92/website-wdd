<?php

namespace App\Filament\Resources\Rapats\Pages;

use App\Filament\Resources\Rapats\RapatResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewRapat extends ViewRecord
{
    protected static string $resource = RapatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
