<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Services\DashboardService;
use App\Services\CashflowService;

class StatsOverviewWidget extends Widget
{
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';
    
    protected string $view = 'filament.widgets.stats-overview-widget';

    protected function getViewData(): array
    {
        $user = auth()->user();
        $stats = DashboardService::getStatistics();
        
        $data = [
            'keuangan' => null,
            'stats' => null,
            'showGlobalStats' => false,
            'user' => $user,
        ];

        // Semua user aktif: tampilkan kewajiban keuangan pribadi
        if ($user && $user->is_active) {
            $data['keuangan'] = DashboardService::getKeuanganAnggota($user);
        }

        // Tampilkan statistik global ke seluruh user aktif untuk transparansi
        if ($user && $user->is_active) {
            $data['stats'] = $stats;
            $data['showGlobalStats'] = true;
        }

        return $data;
    }
}
