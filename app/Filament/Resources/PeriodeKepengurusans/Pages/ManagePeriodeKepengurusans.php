<?php

namespace App\Filament\Resources\PeriodeKepengurusans\Pages;

use App\Filament\Resources\PeriodeKepengurusans\PeriodeKepengurusanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManagePeriodeKepengurusans extends ManageRecords
{
    protected static string $resource = PeriodeKepengurusanResource::class;

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


