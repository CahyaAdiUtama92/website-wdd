<?php

namespace App\Filament\Resources\Rapats\Pages;

use App\Filament\Resources\Rapats\RapatResource;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;

class CreateRapat extends CreateRecord
{
    protected static string $resource = RapatResource::class;

    /**
     * Override form khusus untuk halaman Create.
     * Hanya menampilkan field Informasi — Absensi & Notulen belum tersedia
     * saat status masih "dijadwalkan".
     */
    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('judul')
                ->label('Judul Rapat')
                ->required()
                ->maxLength(255)
                ->columnSpanFull(),

            DatePicker::make('tanggal')
                ->label('Tanggal')
                ->required()
                ->minDate(today())
                ->live(),

            TimePicker::make('waktu')
                ->label('Waktu')
                ->required()
                ->seconds(false)
                ->rules([
                    fn (Get $get) => function (string $attribute, mixed $value, \Closure $fail) use ($get) {
                        $tanggal = $get('tanggal');
                        if (! $tanggal || ! $value) {
                            return;
                        }
                        $jadwal = \Carbon\Carbon::parse("{$tanggal} {$value}");
                        if ($jadwal->isPast()) {
                            $fail('Waktu rapat sudah lewat. Pilih waktu yang akan datang.');
                        }
                    },
                ]),

            TextInput::make('tempat')
                ->label('Tempat')
                ->required()
                ->maxLength(255),

            RichEditor::make('agenda')
                ->label('Agenda')
                ->required()
                ->columnSpanFull(),

            Textarea::make('keterangan')
                ->label('Keterangan')
                ->columnSpanFull()
                ->rows(3),
        ])->columns(3);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->getRecord()]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['status'] = 'dijadwalkan';
        $data['absensi_ditutup'] = false;
        $data['created_by'] = auth()->id();

        return $data;
    }
}
