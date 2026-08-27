<?php

namespace App\Filament\Resources\StrukturOrganisasis\Pages;

use App\Filament\Resources\StrukturOrganisasis\StrukturOrganisasiResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageStrukturOrganisasis extends ManageRecords
{
    protected static string $resource = StrukturOrganisasiResource::class;

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


