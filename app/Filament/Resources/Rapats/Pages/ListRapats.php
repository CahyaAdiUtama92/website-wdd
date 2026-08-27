<?php

namespace App\Filament\Resources\Rapats\Pages;

use App\Filament\Resources\Rapats\RapatResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\Action;

class ListRapats extends ListRecords
{
    protected static string $resource = RapatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Jadwalkan Rapat')
                ->icon('heroicon-o-plus'),
        ];
    }
}
