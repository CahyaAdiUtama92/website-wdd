<?php

namespace App\Filament\Resources\Pengaturans;

use App\Filament\Resources\Pengaturans\Pages\ManagePengaturans;
use App\Models\Pengaturan;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PengaturanResource extends Resource
{
    protected static ?string $model = Pengaturan::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Sistem';
    protected static ?string $navigationLabel = 'Pengaturan';
    protected static ?string $modelLabel = 'Pengaturan';
    protected static ?string $pluralModelLabel = 'Pengaturan';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\TextInput::make('key')->required()->unique(ignoreRecord: true),
                \Filament\Forms\Components\Textarea::make('value')->required()->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('key')->searchable(),
                \Filament\Tables\Columns\TextColumn::make('value')->limit(50),
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
            'index' => ManagePengaturans::route('/'),
        ];
    }
}

