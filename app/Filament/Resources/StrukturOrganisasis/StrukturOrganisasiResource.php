<?php

namespace App\Filament\Resources\StrukturOrganisasis;

use App\Filament\Resources\StrukturOrganisasis\Pages\ManageStrukturOrganisasis;
use App\Models\StrukturOrganisasi;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class StrukturOrganisasiResource extends Resource
{
    protected static ?string $model = StrukturOrganisasi::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Organisasi';
    protected static ?string $navigationLabel = 'Struktur Organisasi';
    protected static ?string $modelLabel = 'Struktur Organisasi';
    protected static ?string $pluralModelLabel = 'Struktur Organisasi';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Select::make('periode_kepengurusan_id')
                    ->relationship('periodeKepengurusan', 'tahun_mulai')
                    ->getOptionLabelFromRecordUsing(fn (\App\Models\PeriodeKepengurusan $record) => "{$record->tahun_mulai} - {$record->tahun_selesai}")
                    ->required()
                    ->label('Periode'),
                \Filament\Forms\Components\Select::make('jabatan_id')
                    ->relationship('jabatan', 'name')
                    ->required()
                    ->label('Jabatan'),
                \Filament\Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->label('Pejabat (Anggota)'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->content(fn () => view('filament.tables.components.struktur-bagan'))
            ->paginated(false)
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('periode_kepengurusan_id')
                    ->label('Periode')
                    ->options(fn () => \App\Models\PeriodeKepengurusan::all()->mapWithKeys(fn ($periode) => [$periode->id => "{$periode->tahun_mulai} - {$periode->tahun_selesai}"])->toArray())
                    ->default(fn () => \App\Models\PeriodeKepengurusan::where('is_active', true)->value('id')),
            ])
            ->recordActions([
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
            'index' => ManageStrukturOrganisasis::route('/'),
        ];
    }
}

