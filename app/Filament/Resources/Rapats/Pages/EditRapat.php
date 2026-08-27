<?php

namespace App\Filament\Resources\Rapats\Pages;

use App\Filament\Resources\Rapats\RapatResource;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditRapat extends EditRecord
{
    protected static string $resource = RapatResource::class;

    /**
     * Saat halaman Edit dibuka: jika rapat sedang berlangsung
     * tapi absensi masih kosong, otomatis populate semua user aktif.
     * Ini menangani kasus rapat yang sudah berlangsung sebelum
     * fitur auto-populate diterapkan.
     */
    public function mount(int|string $record): void
    {
        parent::mount($record);

        $rapat = $this->getRecord();

        if ($rapat->isBerlangsung() && ! $rapat->absensi_ditutup && $rapat->absensis()->count() === 0) {
            User::where('is_active', true)
                ->where('is_super_admin', false)
                ->each(function (User $user) use ($rapat) {
                $rapat->absensis()->firstOrCreate(
                    ['user_id' => $user->id],
                    ['status' => 'Hadir', 'keterangan' => null]
                );
            });

            $this->fillForm();
        }
    }

    protected function getHeaderActions(): array
    {
        $record = $this->getRecord();

        return [
            // ── Mulai Rapat ────────────────────────────────────────────────────
            Action::make('mulai_rapat')
                ->label('Mulai Rapat')
                ->icon('heroicon-o-play')
                ->color('success')
                ->visible(fn () => $record->isDijadwalkan())
                ->requiresConfirmation()
                ->modalHeading('Mulai Rapat?')
                ->modalDescription('Rapat akan dimulai dan status berubah menjadi Berlangsung. Absensi semua anggota aktif akan disiapkan secara otomatis.')
                ->modalSubmitActionLabel('Ya, Mulai Rapat')
                ->action(function () use ($record) {
                    // Pre-populate absensi untuk semua user aktif (bukan super admin)
                    User::where('is_active', true)
                        ->where('is_super_admin', false)
                        ->each(function (User $user) use ($record) {
                            $record->absensis()->firstOrCreate(
                                ['user_id' => $user->id],
                                ['status' => 'Hadir', 'keterangan' => null]
                            );
                        });

                    $record->update(['status' => 'berlangsung']);

                    Notification::make()
                        ->title('Rapat dimulai')
                        ->body('Status berubah menjadi Berlangsung. Daftar absensi telah disiapkan.')
                        ->success()
                        ->send();

                    // Redirect ke halaman yang sama agar status baru ter-render penuh
                    // sehingga tab Absensi langsung aktif tanpa perlu refresh manual
                    $this->redirect(
                        static::getResource()::getUrl('edit', ['record' => $record->getKey()])
                    );
                }),

            // ── Tutup Absensi ──────────────────────────────────────────────────
            Action::make('tutup_absensi')
                ->label('Tutup Absensi')
                ->icon('heroicon-o-clipboard-document-check')
                ->color('warning')
                ->visible(fn () => $record->isBerlangsung() && ! $record->absensi_ditutup)
                ->requiresConfirmation()
                ->modalHeading('Tutup Absensi?')
                ->modalDescription('Absensi akan ditandai sebagai final. Anda dapat melanjutkan pengisian notulen setelah ini.')
                ->modalSubmitActionLabel('Ya, Tutup Absensi')
                ->action(function () use ($record) {
                    $record->update(['absensi_ditutup' => true]);

                    Notification::make()
                        ->title('Absensi ditutup')
                        ->body('Absensi telah final. Silakan susun notulen rapat.')
                        ->warning()
                        ->send();

                    $this->redirect(
                        static::getResource()::getUrl('edit', ['record' => $record->getKey()])
                    );
                }),

            // ── Selesaikan Rapat ───────────────────────────────────────────────
            Action::make('selesaikan_rapat')
                ->label('Selesaikan Rapat')
                ->icon('heroicon-o-check-badge')
                ->color('primary')
                ->visible(fn () => $record->isBerlangsung() && $record->absensi_ditutup)
                ->requiresConfirmation()
                ->modalHeading('Selesaikan Rapat?')
                ->modalDescription('Rapat akan diselesaikan dan semua data menjadi arsip (read-only).')
                ->modalSubmitActionLabel('Ya, Selesaikan Rapat')
                ->action(function () use ($record) {
                    // Validasi: notulen wajib ada
                    if (! $record->notulen || ! $record->notulen->isi_notulen) {
                        Notification::make()
                            ->title('Gagal Menyelesaikan Rapat')
                            ->body('Notulen rapat belum diisi. Harap isi notulen sebelum menyelesaikan rapat.')
                            ->danger()
                            ->send();
                        return;
                    }

                    $record->update(['status' => 'selesai']);

                    Notification::make()
                        ->title('Rapat selesai')
                        ->body('Rapat telah diselesaikan dan data tersimpan sebagai arsip.')
                        ->success()
                        ->send();

                    // Redirect ke daftar rapat setelah selesai
                    $this->redirect(static::getResource()::getUrl('index'));
                }),

            DeleteAction::make(),
        ];
    }
}
