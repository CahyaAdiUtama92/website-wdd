<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\Rapat;

class RapatWidget extends Widget
{
    protected static ?int $sort = 3;
    protected string $view = 'filament.widgets.rapat-widget';
    protected int | string | array $columnSpan = 'full';

    protected function getViewData(): array
    {
        return [
            'rapat' => Rapat::where('status', 'dijadwalkan')
                            ->where('tanggal', '>=', date('Y-m-d'))
                            ->orderBy('tanggal', 'asc')
                            ->first(),
        ];
    }
}
