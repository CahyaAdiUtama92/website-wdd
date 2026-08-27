<?php

namespace App\Filament\Resources\Pengumumen\Pages;

use App\Filament\Resources\Pengumumen\PengumumanResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePengumuman extends CreateRecord
{
    protected static string $resource = PengumumanResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['dibuat_oleh'] = auth()->id();
        return $data;
    }
}
