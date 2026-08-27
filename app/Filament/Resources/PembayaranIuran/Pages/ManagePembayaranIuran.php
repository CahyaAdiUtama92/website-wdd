<?php

namespace App\Filament\Resources\PembayaranIuran\Pages;

use App\Filament\Resources\PembayaranIuran\PembayaranIuranResource;
use App\Models\IuranAnggota;
use App\Models\Pengaturan;
use App\Models\User;
use App\Services\CashflowService;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\HtmlString;

class ManagePembayaranIuran extends ManageRecords
{
    protected static string $resource = PembayaranIuranResource::class;

    protected static ?string $breadcrumb = 'Daftar';

    public function hasResourceBreadcrumbs(): bool
    {
        return true;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('bayar_iuran')
                ->label('Bayar Iuran')
                ->icon('heroicon-o-credit-card')
                ->color('primary')
                ->visible(fn () => auth()->user()->can('create', IuranAnggota::class))
                ->form([
                    Select::make('user_id')
                        ->label('Anggota')
                        ->options(
                            User::where('is_active', true)
                                ->where('is_super_admin', false)
                                ->whereNotNull('role_id')
                                ->orderBy('name')
                                ->pluck('name', 'id')
                        )
                        ->required()
                        ->searchable()
                        ->placeholder('Pilih anggota...'),

                    Select::make('bulan_mulai')
                        ->label('Bulan Mulai')
                        ->options(self::getBulanOptions())
                        ->required()
                        ->default((int) now()->format('n'))
                        ->live(),

                    Select::make('bulan_akhir')
                        ->label('Bulan Akhir')
                        ->options(self::getBulanOptions())
                        ->required()
                        ->default((int) now()->format('n'))
                        ->rules([
                            fn ($get) => function (string $attribute, $value, \Closure $fail) use ($get) {
                                if ((int) $value < (int) $get('bulan_mulai')) {
                                    $fail('Bulan akhir tidak boleh lebih awal dari bulan mulai.');
                                }
                            },
                        ]),

                    TextInput::make('tahun')
                        ->label('Tahun')
                        ->numeric()
                        ->required()
                        ->default((int) now()->format('Y'))
                        ->minValue(2020)
                        ->maxValue(2100),
                ])
                ->requiresConfirmation()
                ->modalHeading('Konfirmasi Pembayaran Iuran')
                ->modalDescription(fn (array $data): HtmlString => $this->buildPreview($data))
                ->modalSubmitActionLabel('Ya, Simpan')
                ->modalCancelActionLabel('Batal')
                ->action(function (array $data): void {
                    try {
                        $result = CashflowService::recordBulkIuran(
                            userId:     (int) $data['user_id'],
                            bulanMulai: (int) $data['bulan_mulai'],
                            bulanAkhir: (int) $data['bulan_akhir'],
                            tahun:      (int) $data['tahun'],
                            dibuatOleh: (int) auth()->id(),
                        );

                        $created = count($result['created']);
                        $skipped = count($result['skipped']);

                        if ($created === 0) {
                            Notification::make()
                                ->title('Semua Bulan Sudah Dibayar')
                                ->body('Tidak ada bulan baru yang dicatat. Semua bulan dalam rentang ini sudah memiliki pembayaran.')
                                ->warning()
                                ->send();

                            return;
                        }

                        $nominal    = Pengaturan::getNominalIuranBulanan();
                        $totalNominal = $created * $nominal;

                        $body = "{$created} bulan berhasil dicatat. Total: Rp " . number_format($totalNominal, 0, ',', '.');

                        if ($skipped > 0) {
                            $body .= " ({$skipped} bulan dilewati karena sudah dibayar)";
                        }

                        Notification::make()
                            ->title('Pembayaran Berhasil')
                            ->body($body)
                            ->success()
                            ->send();

                    } catch (\RuntimeException $e) {
                        Notification::make()
                            ->title('Gagal Menyimpan')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }

    // -----------------------------------------------------------------------
    // Helpers
    // -----------------------------------------------------------------------

    /**
     * Membangun HTML preview yang ditampilkan di modal konfirmasi.
     * Menampilkan daftar bulan beserta status (akan dibuat / sudah dibayar).
     */
    private function buildPreview(array $data): HtmlString
    {
        $bulanMulai = (int) ($data['bulan_mulai'] ?? 0);
        $bulanAkhir = (int) ($data['bulan_akhir'] ?? 0);
        $tahun      = (int) ($data['tahun'] ?? 0);
        $userId     = (int) ($data['user_id'] ?? 0);

        $bulanNames = self::getBulanNames();
        $nominal    = Pengaturan::getNominalIuranBulanan();

        // Cek bulan mana yang sudah dibayar (query sekali untuk seluruh range)
        $sudahDibayar = IuranAnggota::where('user_id', $userId)
            ->where('tahun', $tahun)
            ->whereBetween('bulan', [$bulanMulai, $bulanAkhir])
            ->pluck('bulan')
            ->all();

        $namaMember = User::find($userId)?->name ?? '';

        $rows      = '';
        $countBaru = 0;

        for ($bulan = $bulanMulai; $bulan <= $bulanAkhir; $bulan++) {
            $namaBulan = $bulanNames[$bulan] ?? $bulan;

            if (in_array($bulan, $sudahDibayar)) {
                $rows .= "<li style='color:#f59e0b;'>⏭&nbsp; {$namaBulan} {$tahun} &mdash; <em>sudah dibayar</em></li>";
            } else {
                $rows .= "<li style='color:#22c55e;'>✓&nbsp; {$namaBulan} {$tahun}</li>";
                $countBaru++;
            }
        }

        $totalNominal  = $countBaru * $nominal;
        $formattedNominal = 'Rp ' . number_format($totalNominal, 0, ',', '.');
        $countDilewati = ($bulanAkhir - $bulanMulai + 1) - $countBaru;

        $html = "
            <div style='font-family:inherit; line-height:1.6;'>
                <p style='margin-bottom:8px;'>
                    <strong>Anggota:</strong> {$namaMember}<br>
                    <strong>Tahun:</strong> {$tahun}
                </p>
                <p style='margin-bottom:4px; font-weight:600;'>Rincian bulan:</p>
                <ul style='list-style:none; padding:0; margin:0 0 12px 0;'>
                    {$rows}
                </ul>
                <hr style='margin:8px 0; border-color:#374151;'>
                <p style='margin:4px 0;'><strong>Bulan baru:</strong> {$countBaru} &nbsp;|&nbsp; <strong>Dilewati:</strong> {$countDilewati}</p>
                <p style='margin:4px 0;'><strong>Total Nominal:</strong> {$formattedNominal}</p>
            </div>
        ";

        return new HtmlString($html);
    }

    /** Opsi bulan untuk Select field. */
    private static function getBulanOptions(): array
    {
        return self::getBulanNames();
    }

    /** Mapping integer bulan → nama Bahasa Indonesia. */
    private static function getBulanNames(): array
    {
        return [
            1  => 'Januari',
            2  => 'Februari',
            3  => 'Maret',
            4  => 'April',
            5  => 'Mei',
            6  => 'Juni',
            7  => 'Juli',
            8  => 'Agustus',
            9  => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];
    }
}

