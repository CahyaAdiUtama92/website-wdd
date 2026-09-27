<?php

namespace App\Filament\Pages;

use App\Models\User;
use App\Models\IuranAnggota;
use App\Services\CashflowService;
use Filament\Pages\Page;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Select;
use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Infolist;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;

class TunggakanIuran extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-exclamation-circle';
    protected static string|\UnitEnum|null $navigationGroup = 'Keuangan';
    protected static ?string $navigationLabel = 'Tunggakan Iuran';
    protected static ?string $title = 'Tunggakan Iuran';
    protected string $view = 'filament.pages.tunggakan-iuran';
    protected static ?int $navigationSort = 4;

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->is_super_admin || auth()->user()->role?->name === 'Bendahara';
    }

    public function mount(): void
    {
        abort_unless(static::shouldRegisterNavigation(), 403);
    }

    public function table(Table $table): Table
    {
        $bulanNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        return $table
            ->query(
                User::query()
                    ->where('is_active', true)
                    ->where('is_super_admin', false)
                    ->whereNotNull('role_id')
                    ->with(['iuranAnggota.arusKas'])
            )
            ->columns([
                TextColumn::make('nia')
                    ->label('NIA')
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Nama Anggota')
                    ->searchable(),
                TextColumn::make('status_periode')
                    ->label(function () {
                        $state = $this->getTableFilterState('periode') ?? [];
                        $bulan = (int) ($state['bulan'] ?? now()->format('n'));
                        $tahun = (int) ($state['tahun'] ?? now()->format('Y'));
                        $bulanNames = [
                            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
                            4 => 'April', 5 => 'Mei', 6 => 'Juni',
                            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
                            10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                        ];
                        return 'Status ' . ($bulanNames[$bulan] ?? '') . ' ' . $tahun;
                    })
                    ->getStateUsing(function (User $record) {
                        $state = $this->getTableFilterState('periode') ?? [];
                        $bulan = (int) ($state['bulan'] ?? now()->format('n'));
                        $tahun = (int) ($state['tahun'] ?? now()->format('Y'));
                        $tanggalFilter = \Carbon\Carbon::create($tahun, $bulan, 1)->startOfMonth();
                        $nominalIuran = \App\Models\Pengaturan::getNominalIuranBulanan();

                        return static::getKalkulasiTunggakan($record, $tanggalFilter, $nominalIuran)['status'];
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Lunas' => 'success',
                        'Belum Bayar' => 'danger',
                        'Belum Bergabung' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('total_dibayar')
                    ->label('Total Dibayar')
                    ->getStateUsing(fn (User $record) => CashflowService::getKeuanganAnggota($record)['terbayarkan'])
                    ->money('IDR'),
                TextColumn::make('total_tunggakan')
                    ->label('Tunggakan Kumulatif')
                    ->getStateUsing(function (User $record) {
                        $state = $this->getTableFilterState('periode') ?? [];
                        $bulan = (int) ($state['bulan'] ?? now()->format('n'));
                        $tahun = (int) ($state['tahun'] ?? now()->format('Y'));
                        $tanggalFilter = \Carbon\Carbon::create($tahun, $bulan, 1)->startOfMonth();
                        $nominalIuran = \App\Models\Pengaturan::getNominalIuranBulanan();

                        return static::getKalkulasiTunggakan($record, $tanggalFilter, $nominalIuran)['total_tunggakan'];
                    })
                    ->money('IDR')
                    ->color(fn ($state) => $state > 0 ? 'danger' : 'success'),
            ])
            ->filters([
                Filter::make('periode')
                    ->form([
                        \Filament\Schemas\Components\Grid::make(2)->schema([
                            Select::make('bulan')
                                ->label('Bulan')
                                ->options($bulanNames)
                                ->default(now()->format('n')),
                            Select::make('tahun')
                                ->label('Tahun')
                                ->options(collect(range(now()->year, 2020))->mapWithKeys(fn ($y) => [$y => $y])->toArray())
                                ->default(now()->format('Y')),
                        ])
                    ])
                    ->columnSpan(2)
                    ->query(fn (Builder $query) => $query)
                    ->indicateUsing(function (array $data) use ($bulanNames) {
                        $bulan = (int) ($data['bulan'] ?? now()->format('n'));
                        $tahun = (int) ($data['tahun'] ?? now()->format('Y'));
                        return 'Periode: ' . ($bulanNames[$bulan] ?? '') . ' ' . $tahun;
                    }),
                SelectFilter::make('status_pembayaran')
                    ->label('Status')
                    ->options([
                        'sudah_bayar' => 'Lunas',
                        'belum_bayar' => 'Belum Bayar',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (empty($data['value'])) {
                            return $query;
                        }

                        $state = $this->getTableFilterState('periode') ?? [];
                        $bulan = (int) ($state['bulan'] ?? now()->format('n'));
                        $tahun = (int) ($state['tahun'] ?? now()->format('Y'));
                        $tanggalFilter = \Carbon\Carbon::create($tahun, $bulan, 1)->startOfMonth();
                        $nominalIuran = \App\Models\Pengaturan::getNominalIuranBulanan();

                        $userIds = User::with(['iuranAnggota.arusKas'])
                            ->where('is_active', true)
                            ->where('is_super_admin', false)
                            ->whereNotNull('role_id')
                            ->whereNotNull('tanggal_bergabung')
                            ->get()
                            ->filter(function ($user) use ($tanggalFilter, $nominalIuran, $data) {
                                $kalkulasi = static::getKalkulasiTunggakan($user, $tanggalFilter, $nominalIuran);
                                if ($kalkulasi['status'] === 'Belum Bergabung') {
                                    return false;
                                }
                                
                                return $data['value'] === 'sudah_bayar' ? $kalkulasi['is_lunas'] : !$kalkulasi['is_lunas'];
                            })->pluck('id');

                        return $query->whereIn('id', $userIds);
                    }),
            ])
            ->filtersLayout(\Filament\Tables\Enums\FiltersLayout::AboveContent)
            ->filtersFormColumns(3)
            ->headerActions([
                Action::make('export_csv')
                    ->label('Export CSV')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(function ($livewire) {
                        $state = $livewire->getTableFilterState('periode') ?? [];
                        $bulan = (int) ($state['bulan'] ?? now()->format('n'));
                        $tahun = (int) ($state['tahun'] ?? now()->format('Y'));
                        $tanggalFilter = \Carbon\Carbon::create($tahun, $bulan, 1)->startOfMonth();
                        
                        $statusState = $livewire->getTableFilterState('status_pembayaran') ?? [];
                        $statusValue = $statusState['value'] ?? null;
                        
                        $bulanNames = [
                            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
                            4 => 'April', 5 => 'Mei', 6 => 'Juni',
                            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
                            10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                        ];
                        $periodeNama = ($bulanNames[$bulan] ?? '') . ' ' . $tahun;
                        
                        $filename = 'tunggakan-iuran-';
                        if ($statusValue === 'sudah_bayar') {
                            $filename .= 'lunas-';
                        } elseif ($statusValue === 'belum_bayar') {
                            $filename .= 'belum-bayar-';
                        }
                        $filename .= strtolower($bulanNames[$bulan] ?? '') . '-' . $tahun . '.csv';
                        
                        // We use getFilteredTableQuery() to ensure search and filters are applied exactly like the table
                        $query = $livewire->getFilteredTableQuery();
                        $query->with(['iuranAnggota.arusKas']);
                        $users = $query->get();
                        
                        $nominalIuran = \App\Models\Pengaturan::getNominalIuranBulanan();
                        
                        $headers = [
                            "Content-type"        => "text/csv; charset=UTF-8",
                            "Content-Disposition" => "attachment; filename=\"$filename\"",
                            "Pragma"              => "no-cache",
                            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                            "Expires"             => "0"
                        ];
                        
                        $columns = [
                            'No', 
                            'NIA', 
                            'Nama Anggota', 
                            'Tanggal Bergabung', 
                            "Status $periodeNama", 
                            "Kewajiban s/d $periodeNama", 
                            'Total Pembayaran Anggota', 
                            "Tunggakan s/d $periodeNama", 
                            'Bulan Belum Dibayar'
                        ];
                        
                        $callback = function() use ($users, $columns, $tanggalFilter, $nominalIuran) {
                            $file = fopen('php://output', 'w');
                            fputs($file, "\xEF\xBB\xBF");
                            fputcsv($file, $columns, ',');
                            
                            $no = 1;
                            foreach ($users as $user) {
                                $kalkulasi = static::getKalkulasiTunggakan($user, $tanggalFilter, $nominalIuran);
                                
                                $tanggalBergabungStr = $user->tanggal_bergabung 
                                    ? \Carbon\Carbon::parse($user->tanggal_bergabung)->format('d-m-Y') 
                                    : '-';
                                    
                                $bulanBelumDibayarStrs = [];
                                foreach ($kalkulasi['belum_dibayar_list'] as $belum) {
                                    $bulanBelumDibayarStrs[] = $belum['periode'];
                                }
                                $bulanBelumDibayar = count($bulanBelumDibayarStrs) > 0 ? implode(', ', $bulanBelumDibayarStrs) : '-';
                                
                                $row = [
                                    $no++,
                                    $user->nia ?? '-',
                                    $user->name,
                                    $tanggalBergabungStr,
                                    $kalkulasi['status'],
                                    $kalkulasi['kewajiban_kumulatif'],
                                    $kalkulasi['terbayarkan'],
                                    $kalkulasi['total_tunggakan'],
                                    $bulanBelumDibayar
                                ];
                                
                                fputcsv($file, $row, ',');
                            }
                            fclose($file);
                        };
                        
                        return response()->streamDownload($callback, $filename, $headers);
                    })
            ])
            ->actions([
                Action::make('detail')
                    ->label('Detail')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Detail Tunggakan')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->infolist(function ($infolist, User $record) {
                        $state = $this->getTableFilterState('periode') ?? [];
                        $bulan = (int) ($state['bulan'] ?? now()->format('n'));
                        $tahun = (int) ($state['tahun'] ?? now()->format('Y'));
                        $tanggalFilter = \Carbon\Carbon::create($tahun, $bulan, 1)->startOfMonth();
                        $nominalIuran = \App\Models\Pengaturan::getNominalIuranBulanan();
                        
                        $bulanNames = [
                            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
                            4 => 'April', 5 => 'Mei', 6 => 'Juni',
                            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
                            10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                        ];
                        $periodeNama = ($bulanNames[$bulan] ?? '') . ' ' . $tahun;

                        $record->loadMissing(['iuranAnggota.arusKas']);
                        $kalkulasi = static::getKalkulasiTunggakan($record, $tanggalFilter, $nominalIuran);

                        $belumDibayar = $kalkulasi['belum_dibayar_list'];
                        $record->belum_dibayar_list = $belumDibayar;
                        $totalKewajiban = $kalkulasi['kewajiban_kumulatif'];
                        $terbayarkan = $kalkulasi['terbayarkan'];
                        $totalTunggakan = $kalkulasi['total_tunggakan'];

                        return $infolist
                            ->record($record)
                            ->schema([
                                Section::make('Informasi Anggota')
                                    ->schema([
                                        TextEntry::make('name')->label('Nama'),
                                        TextEntry::make('nia')->label('NIA'),
                                        TextEntry::make('tanggal_bergabung')->label('Tanggal Bergabung')->date('d F Y'),
                                    ])->columns(3),
                                Section::make('Ringkasan Iuran sampai ' . $periodeNama)
                                    ->schema([
                                        TextEntry::make('total_kewajiban')
                                            ->label('Kewajiban s/d ' . $periodeNama)
                                            ->state('Rp ' . number_format($totalKewajiban, 0, ',', '.')),
                                        TextEntry::make('total_dibayar')
                                            ->label('Total Pembayaran Anggota')
                                            ->state('Rp ' . number_format($terbayarkan, 0, ',', '.')),
                                        TextEntry::make('total_tunggakan')
                                            ->label('Tunggakan s/d ' . $periodeNama)
                                            ->state('Rp ' . number_format($totalTunggakan, 0, ',', '.'))
                                            ->color($totalTunggakan > 0 ? 'danger' : 'success'),
                                    ])->columns(3),
                                Section::make('Bulan Belum Dibayar')
                                    ->schema(
                                        count($belumDibayar) > 0 
                                            ? [
                                                RepeatableEntry::make('belum_dibayar_list')
                                                    ->hiddenLabel()
                                                    ->schema([
                                                        TextEntry::make('periode')->label('Periode'),
                                                        TextEntry::make('nominal')->label('Nominal (Rp)')->money('IDR'),
                                                    ])
                                                    ->columns(2)
                                            ] 
                                            : [
                                                TextEntry::make('tidak_ada_tunggakan')
                                                    ->hiddenLabel()
                                                    ->state('Tidak ada tunggakan sampai ' . $periodeNama . '.')
                                                    ->color('success')
                                            ]
                                    ),
                            ]);
                    }),
            ]);
    }

    public function getStats(): array
    {
        $state = $this->getTableFilterState('periode') ?? [];
        $bulan = (int) ($state['bulan'] ?? now()->format('n'));
        $tahun = (int) ($state['tahun'] ?? now()->format('Y'));
        
        $tanggalFilter = \Carbon\Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $nominalIuran = \App\Models\Pengaturan::getNominalIuranBulanan();

        $users = User::with(['iuranAnggota.arusKas'])
            ->where('is_active', true)
            ->where('is_super_admin', false)
            ->whereNotNull('role_id')
            ->whereNotNull('tanggal_bergabung')
            ->get()
            ->filter(function ($user) use ($tanggalFilter) {
                $tanggalBergabung = \Carbon\Carbon::parse($user->tanggal_bergabung)->startOfMonth();
                return $tanggalBergabung->lte($tanggalFilter);
            });

        $totalAnggota = $users->count();
        $sudahMembayar = 0;
        $totalTunggakan = 0;

        foreach ($users as $user) {
            $kalkulasi = static::getKalkulasiTunggakan($user, $tanggalFilter, $nominalIuran);

            if ($kalkulasi['is_lunas']) {
                $sudahMembayar++;
            } else {
                $totalTunggakan += $kalkulasi['total_tunggakan'];
            }
        }

        $belumMembayar = $totalAnggota - $sudahMembayar;

        $bulanNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        return [
            'total' => $totalAnggota,
            'sudah' => $sudahMembayar,
            'belum' => $belumMembayar,
            'tunggakan' => $totalTunggakan,
            'periode_nama' => ($bulanNames[$bulan] ?? '') . ' ' . $tahun
        ];
    }

    public static function getKalkulasiTunggakan(User $record, \Carbon\Carbon $tanggalFilter, int $nominalIuran): array
    {
        if (!$record->tanggal_bergabung) {
            return [
                'status' => 'Belum Bergabung',
                'kewajiban_kumulatif' => 0,
                'terbayarkan' => 0,
                'total_tunggakan' => 0,
                'is_lunas' => false,
                'belum_dibayar_list' => [],
            ];
        }

        $tanggalBergabung = \Carbon\Carbon::parse($record->tanggal_bergabung)->startOfMonth();
        if ($tanggalFilter->lt($tanggalBergabung)) {
            return [
                'status' => 'Belum Bergabung',
                'kewajiban_kumulatif' => 0,
                'terbayarkan' => 0,
                'total_tunggakan' => 0,
                'is_lunas' => false,
                'belum_dibayar_list' => [],
            ];
        }

        $jumlahBulanKewajiban = (int) $tanggalBergabung->diffInMonths($tanggalFilter) + 1;
        $kewajibanKumulatif = $jumlahBulanKewajiban * $nominalIuran;

        $terbayarkan = (float) $record->iuranAnggota->sum(fn ($iuran) => $iuran->arusKas?->jumlah ?? 0);

        $totalTunggakan = max(0, $kewajibanKumulatif - $terbayarkan);
        $isLunas = $terbayarkan >= $kewajibanKumulatif;
        $status = $isLunas ? 'Lunas' : 'Belum Bayar';

        $belumDibayar = [];
        $sisaTerbayarkan = $terbayarkan;
        
        $bulanNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        $current = $tanggalBergabung->copy();
        while ($current->lte($tanggalFilter)) {
            if ($sisaTerbayarkan >= $nominalIuran) {
                $sisaTerbayarkan -= $nominalIuran;
            } else {
                $kekurangan = $nominalIuran - $sisaTerbayarkan;
                $sisaTerbayarkan = 0;
                $belumDibayar[] = [
                    'periode' => $bulanNames[(int)$current->format('n')] . ' ' . $current->format('Y'),
                    'nominal' => $kekurangan
                ];
            }
            $current->addMonth();
        }

        return [
            'status' => $status,
            'kewajiban_kumulatif' => $kewajibanKumulatif,
            'terbayarkan' => $terbayarkan,
            'total_tunggakan' => $totalTunggakan,
            'is_lunas' => $isLunas,
            'belum_dibayar_list' => $belumDibayar,
        ];
    }
}
