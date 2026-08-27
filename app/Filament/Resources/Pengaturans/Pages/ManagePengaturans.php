<?php

namespace App\Filament\Resources\Pengaturans\Pages;

use App\Filament\Resources\Pengaturans\PengaturanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManagePengaturans extends ManageRecords
{
    protected static string $resource = PengaturanResource::class;

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


