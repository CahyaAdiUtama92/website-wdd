<?php

namespace App\Filament\Resources\ArusKas;

use App\Filament\Resources\ArusKas\Pages\ManageArusKas;
use App\Models\ArusKas;
use App\Models\KategoriArusKas;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ArusKasResource extends Resource
{
    protected static ?string $model = ArusKas::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Keuangan';
    protected static ?string $navigationLabel = 'Arus Kas';
    protected static ?string $modelLabel = 'Transaksi';
    protected static ?string $pluralModelLabel = 'Arus Kas';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\DatePicker::make('tanggal_transaksi')
                    ->label('Tanggal Transaksi')
                    ->required()
                    ->default(now()),

                \Filament\Forms\Components\Select::make('tipe')
                    ->label('Tipe')
                    ->options([
                        'pemasukan'  => 'Pemasukan',
                        'pengeluaran' => 'Pengeluaran',
                    ])
                    ->required()
                    ->live(),

                \Filament\Forms\Components\Select::make('kategori_id')
                    ->label('Kategori')
                    ->options(fn ($get) => KategoriArusKas::when(
                        $get('tipe'),
                        fn ($q, $tipe) => $q->where('tipe', $tipe)
                    )->pluck('nama', 'id'))
                    ->required()
                    ->searchable(),

                \Filament\Forms\Components\TextInput::make('jumlah')
                    ->label('Jumlah')
                    ->numeric()
                    ->required()
                    ->prefix('Rp'),

                \Filament\Forms\Components\Textarea::make('keterangan')
                    ->label('Keterangan')
                    ->nullable()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('tanggal_transaksi')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('tipe')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pemasukan'  => 'success',
                        'pengeluaran' => 'danger',
                        default       => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                \Filament\Tables\Columns\TextColumn::make('kategori.nama')
                    ->label('Kategori')
                    ->searchable(),
                \Filament\Tables\Columns\TextColumn::make('user.name')
                    ->label('Anggota')
                    ->placeholder('-')
                    ->searchable(),
                \Filament\Tables\Columns\TextColumn::make('jumlah')
                    ->label('Jumlah')
                    ->money('IDR')
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(40)
                    ->placeholder('-'),
                \Filament\Tables\Columns\TextColumn::make('dibuatOleh.name')
                    ->label('Dicatat Oleh')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('tipe')
                    ->label('Tipe')
                    ->options([
                        'pemasukan'  => 'Pemasukan',
                        'pengeluaran' => 'Pengeluaran',
                    ]),
                SelectFilter::make('kategori_id')
                    ->label('Kategori')
                    ->relationship('kategori', 'nama'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('tanggal_transaksi', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageArusKas::route('/'),
        ];
    }
}
