<?php

namespace App\Filament\Resources\KategoriArusKas;

use App\Filament\Resources\KategoriArusKas\Pages\ManageKategoriArusKas;
use App\Models\KategoriArusKas;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class KategoriArusKasResource extends Resource
{
    protected static ?string $model = KategoriArusKas::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Keuangan';
    protected static ?string $navigationLabel = 'Kategori Arus Kas';
    protected static ?string $modelLabel = 'Kategori Arus Kas';
    protected static ?string $pluralModelLabel = 'Kategori Arus Kas';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\TextInput::make('nama')
                    ->label('Nama Kategori')
                    ->required(),
                \Filament\Forms\Components\Select::make('tipe')
                    ->label('Tipe')
                    ->options([
                        'pemasukan'  => 'Pemasukan',
                        'pengeluaran' => 'Pengeluaran',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Kategori')
                    ->searchable(),
                \Filament\Tables\Columns\TextColumn::make('tipe')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pemasukan'  => 'success',
                        'pengeluaran' => 'danger',
                        default       => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
            ])
            ->filters([
                //
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
            'index' => ManageKategoriArusKas::route('/'),
        ];
    }
}
