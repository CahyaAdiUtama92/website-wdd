<?php

namespace App\Filament\Resources\PeriodeKepengurusans;

use App\Filament\Resources\PeriodeKepengurusans\Pages\ManagePeriodeKepengurusans;
use App\Models\PeriodeKepengurusan;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PeriodeKepengurusanResource extends Resource
{
    protected static ?string $model = PeriodeKepengurusan::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Organisasi';
    protected static ?string $navigationLabel = 'Periode Kepengurusan';
    protected static ?string $modelLabel = 'Periode Kepengurusan';
    protected static ?string $pluralModelLabel = 'Periode Kepengurusan';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendar;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\TextInput::make('tahun_mulai')
                    ->required()
                    ->numeric()
                    ->label('Tahun Mulai')
                    ->unique(
                        ignoreRecord: true,
                        modifyRuleUsing: fn (\Illuminate\Validation\Rules\Unique $rule, $get) => $rule->where('tahun_selesai', $get('tahun_selesai'))
                    )
                    ->validationMessages([
                        'unique' => 'Periode kepengurusan ini sudah ada.',
                    ]),
                \Filament\Forms\Components\TextInput::make('tahun_selesai')->required()->numeric()->label('Tahun Selesai'),
                \Filament\Forms\Components\Toggle::make('is_active')->default(false)->label('Status Aktif'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('tahun_mulai')->label('Tahun Mulai'),
                \Filament\Tables\Columns\TextColumn::make('tahun_selesai')->label('Tahun Selesai'),
                \Filament\Tables\Columns\IconColumn::make('is_active')->boolean()->label('Aktif'),
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
            'index' => ManagePeriodeKepengurusans::route('/'),
        ];
    }
}

