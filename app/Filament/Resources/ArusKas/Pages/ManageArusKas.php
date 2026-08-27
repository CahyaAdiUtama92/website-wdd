<?php

namespace App\Filament\Resources\ArusKas\Pages;

use App\Filament\Resources\ArusKas\ArusKasResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageArusKas extends ManageRecords
{
    protected static string $resource = ArusKasResource::class;

    protected static ?string $breadcrumb = 'Daftar';

    public function hasResourceBreadcrumbs(): bool
    {
        return true;
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->mutateFormDataUsing(function (array $data): array {
                    // Otomatis mengisi dibuat_oleh dengan user yang sedang login
                    $data['dibuat_oleh'] = auth()->id();
                    return $data;
                })
                ->createAnotherAction(fn ($action) => $action->color('success'))
                ->modalCancelAction(fn ($action) => $action->color('danger')),
        ];
    }
}

