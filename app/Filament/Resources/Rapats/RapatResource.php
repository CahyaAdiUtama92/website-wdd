<?php

namespace App\Filament\Resources\Rapats;

use App\Filament\Resources\Rapats\Pages\CreateRapat;
use App\Filament\Resources\Rapats\Pages\EditRapat;
use App\Filament\Resources\Rapats\Pages\ListRapats;
use App\Filament\Resources\Rapats\Pages\ViewRapat;
use App\Models\Rapat;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;

class RapatResource extends Resource
{
    protected static ?string $model = Rapat::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Rapat';
    protected static ?string $navigationLabel = 'Rapat';
    protected static ?string $modelLabel = 'Rapat';
    protected static ?string $pluralModelLabel = 'Rapat';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMegaphone;

    public static function form(Schema $schema): Schema
    {
        $record = $schema->getRecord();
        $isSelesai = $record && $record->isSelesai();
        $isBerlangsung = $record && $record->isBerlangsung();
        $isDijadwalkan = ! $record || $record->isDijadwalkan();

        // Tab Absensi & Notulen: disabled jika dijadwalkan, readonly jika selesai
        $absensiDisabled = $isDijadwalkan || $isSelesai || ($record && $record->absensi_ditutup);
        $notulenDisabled = $isDijadwalkan || $isSelesai;

        return $schema
            ->components([
                Tabs::make('Tabs')
                    ->tabs([
                        // ── Tab Informasi ──────────────────────────────────────
                        Tab::make('Informasi Rapat')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                TextInput::make('judul')
                                    ->label('Judul Rapat')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpanFull()
                                    ->disabled($isSelesai),

                                DatePicker::make('tanggal')
                                    ->label('Tanggal')
                                    ->required()
                                    ->minDate($isDijadwalkan ? today() : null)
                                    ->live()
                                    ->disabled($isSelesai),

                                TimePicker::make('waktu')
                                    ->label('Waktu')
                                    ->required()
                                    ->seconds(false)
                                    ->rules($isDijadwalkan ? [
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
                                    ] : [])
                                    ->disabled($isSelesai),

                                TextInput::make('tempat')
                                    ->label('Tempat')
                                    ->required()
                                    ->maxLength(255)
                                    ->disabled($isSelesai),

                                TextInput::make('status')
                                    ->label('Status')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->visible(fn () => (bool) $record)
                                    ->formatStateUsing(fn ($state) => match ($state) {
                                        'dijadwalkan' => '📅 Dijadwalkan',
                                        'berlangsung' => '▶️ Berlangsung',
                                        'selesai'     => '✅ Selesai',
                                        default       => $state,
                                    }),

                                RichEditor::make('agenda')
                                    ->label('Agenda')
                                    ->required()
                                    ->columnSpanFull()
                                    ->disabled($isSelesai),

                                Textarea::make('keterangan')
                                    ->label('Keterangan')
                                    ->columnSpanFull()
                                    ->rows(3)
                                    ->disabled($isSelesai),
                            ])
                            ->columns(3),

                        // ── Tab Absensi ────────────────────────────────────────
                        Tab::make('Absensi')
                            ->icon('heroicon-o-user-group')
                            ->badge(fn () => $record ? $record->absensis()->count() : null)
                            ->schema($record ? [
                                Livewire::make(\App\Livewire\AbsensiList::class, [
                                    'rapatId'         => $record->id,
                                    'absensiDisabled' => $absensiDisabled,
                                ])
                                ->key('absensi-list-' . $record->id)
                                ->columnSpanFull(),
                            ] : []),

                        // ── Tab Notulen ────────────────────────────────────────
                        Tab::make('Notulen')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Fieldset::make('Notulen Rapat')
                                    ->relationship('notulen')
                                    ->schema([
                                        RichEditor::make('isi_notulen')
                                            ->label('Isi Notulen')
                                            ->columnSpanFull()
                                            ->disabled($notulenDisabled),

                                        Textarea::make('keputusan_rapat')
                                            ->label('Keputusan Rapat')
                                            ->rows(4)
                                            ->columnSpanFull()
                                            ->disabled($notulenDisabled),

                                        Textarea::make('catatan_tambahan')
                                            ->label('Catatan Tambahan')
                                            ->rows(3)
                                            ->columnSpanFull()
                                            ->disabled($notulenDisabled),

                                        FileUpload::make('file_dokumen')
                                            ->label('File Lampiran')
                                            ->columnSpanFull()
                                            ->downloadable()
                                            ->openable()
                                            ->disabled($notulenDisabled),
                                    ])
                                    ->columns(1),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('judul')
                    ->label('Judul Rapat')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('waktu')
                    ->label('Waktu')
                    ->time('H:i'),

                TextColumn::make('tempat')
                    ->label('Tempat')
                    ->searchable()
                    ->limit(30),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'dijadwalkan' => 'Dijadwalkan',
                        'berlangsung' => 'Berlangsung',
                        'selesai'     => 'Selesai',
                        default       => $state,
                    })
                    ->color(fn ($state) => match ($state) {
                        'dijadwalkan' => 'warning',
                        'berlangsung' => 'success',
                        'selesai'     => 'gray',
                        default       => 'primary',
                    }),

                TextColumn::make('creator.name')
                    ->label('Dibuat Oleh')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->defaultSort('tanggal', 'desc')
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()
                    ->visible(fn ($record) => $record->isSelesai()),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListRapats::route('/'),
            'create' => CreateRapat::route('/create'),
            'view'   => ViewRapat::route('/{record}'),
            'edit'   => EditRapat::route('/{record}/edit'),
        ];
    }
}
