<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\Pengumuman;
use Livewire\WithPagination;

class PengumumanWidget extends Widget
{
    use WithPagination;

    protected static ?int $sort = 2;
    protected string $view = 'filament.widgets.pengumuman-widget';
    protected int | string | array $columnSpan = 'full';

    protected function getViewData(): array
    {
        return [
            'pengumumans' => Pengumuman::with('user')
                ->where('is_published', true)
                ->latest()
                ->paginate(3),
        ];
    }
}
