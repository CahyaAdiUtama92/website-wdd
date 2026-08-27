<?php

namespace App\Filament\Resources\PembayaranIuran;

use App\Filament\Resources\PembayaranIuran\Pages\ManagePembayaranIuran;
use App\Models\IuranAnggota;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PembayaranIuranResource extends Resource
{
    protected static ?string $model = IuranAnggota::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Keuangan';
    protected static ?string $navigationLabel = 'Pembayaran Iuran';
    protected static ?string $modelLabel = 'Pembayaran Iuran';
    protected static ?string $pluralModelLabel = 'Pembayaran Iuran';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCreditCard;

    public static function table(Table $table): Table
    {
        $bulanNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('user.name')
                    ->label('Anggota')
                    ->searchable()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('bulan')
                    ->label('Bulan')
                    ->formatStateUsing(fn (int $state): string => $bulanNames[$state] ?? '-'),
                \Filament\Tables\Columns\TextColumn::make('tahun')
                    ->label('Tahun')
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('arusKas.jumlah')
                    ->label('Nominal')
                    ->money('IDR'),
                \Filament\Tables\Columns\TextColumn::make('created_at')
                    ->label('Dicatat')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('bulan')
                    ->label('Bulan')
                    ->options($bulanNames),
                \Filament\Tables\Filters\SelectFilter::make('tahun')
                    ->label('Tahun')
                    ->options(
                        collect(range(now()->year, 2020))->mapWithKeys(fn ($y) => [$y => $y])->toArray()
                    ),
            ])
            ->recordActions([
                \Filament\Actions\DeleteAction::make(),
            ])
            ->toolbarActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManagePembayaranIuran::route('/'),
        ];
    }
}
