<?php

namespace App\Filament\Resources\Pengumumen;

use App\Filament\Resources\Pengumumen\Pages\CreatePengumuman;
use App\Filament\Resources\Pengumumen\Pages\EditPengumuman;
use App\Filament\Resources\Pengumumen\Pages\ListPengumumen;
use App\Filament\Resources\Pengumumen\Pages\ViewPengumuman;
use App\Models\Pengumuman;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PengumumanResource extends Resource
{
    protected static ?string $model = Pengumuman::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Informasi';
    protected static ?string $navigationLabel = 'Pengumuman';
    protected static ?string $modelLabel = 'Pengumuman';
    protected static ?string $pluralModelLabel = 'Pengumuman';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'judul';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\TextInput::make('judul')
                    ->required()
                    ->maxLength(255),
                \Filament\Forms\Components\Textarea::make('keterangan')
                    ->columnSpanFull(),
                \Filament\Forms\Components\FileUpload::make('file')
                    ->acceptedFileTypes(['application/pdf'])
                    ->maxSize(10240)
                    ->directory('pengumuman')
                    ->helperText('Harap unggah file berformat PDF saja (Maks. 10MB). Jika Anda memiliki file Word, silakan "Save As PDF" terlebih dahulu.')
                    ->required(),
                \Filament\Forms\Components\Toggle::make('is_published')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('judul')->searchable(),
                \Filament\Tables\Columns\TextColumn::make('keterangan')->limit(50),
                \Filament\Tables\Columns\TextColumn::make('user.name')->label('Dibuat Oleh'),
                \Filament\Tables\Columns\IconColumn::make('is_published')->boolean()->label('Status Publish'),
                \Filament\Tables\Columns\TextColumn::make('created_at')->label('Dibuat Pada')->dateTime()->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                \Filament\Actions\ViewAction::make(),
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->toolbarActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\Pengumumen\Pages\ListPengumumen::route('/'),
            'create' => \App\Filament\Resources\Pengumumen\Pages\CreatePengumuman::route('/create'),
            'view' => \App\Filament\Resources\Pengumumen\Pages\ViewPengumuman::route('/{record}'),
            'edit' => \App\Filament\Resources\Pengumumen\Pages\EditPengumuman::route('/{record}/edit'),
        ];
    }
}
