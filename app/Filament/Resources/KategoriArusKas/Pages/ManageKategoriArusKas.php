<?php

namespace App\Filament\Resources\KategoriArusKas\Pages;

use App\Filament\Resources\KategoriArusKas\KategoriArusKasResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageKategoriArusKas extends ManageRecords
{
    protected static string $resource = KategoriArusKasResource::class;

    protected static ?string $breadcrumb = 'Daftar';

    public function hasResourceBreadcrumbs(): bool
    {
        return true;
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
            ->createAnotherAction(fn ($action) => $action->color('success'))
            ->modalCancelAction(fn ($action) => $action->color('danger')),
        ];
    }
}


