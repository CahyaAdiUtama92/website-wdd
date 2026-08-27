<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\ManageUsers;
use App\Models\User;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Keanggotaan';
    protected static ?string $navigationLabel = 'Data Anggota';
    protected static ?string $modelLabel = 'Anggota';
    protected static ?string $pluralModelLabel = 'Data Anggota';

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();

        if (! auth()->user()->is_super_admin) {
            $query->where('is_super_admin', false);
        }

        return $query;
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\TextInput::make('nia')
                    ->label('Nomor Induk Anggota (NIA)')
                    ->default(fn () => \App\Services\GenerateNiaService::generate())
                    ->disabled()
                    ->dehydrated()
                    ->unique(ignoreRecord: true),
                \Filament\Forms\Components\TextInput::make('name')->required()->label('Nama Lengkap')->placeholder('Masukkan nama lengkap anggota...'),
                \Filament\Forms\Components\TextInput::make('email')->email()->required()->label('Email')->placeholder('Masukkan alamat email anggota...')->disabled(fn () => auth()->user()->role?->name !== 'Sekretaris'),
                \Filament\Forms\Components\TextInput::make('no_telp')->tel()->label('No. Telepon')->placeholder('Masukkan nomor telepon anggota...'),
                \Filament\Forms\Components\Textarea::make('alamat')->label('Alamat')->columnSpanFull()->placeholder('Masukkan alamat rumah anggota...'),
                \Filament\Forms\Components\TextInput::make('password')->password()->revealable()->dehydrateStateUsing(fn ($state) => \Illuminate\Support\Facades\Hash::make($state))->dehydrated(fn ($state) => filled($state))->required(fn (string $context): bool => $context === 'create')->placeholder('Masukkan kata sandi anggota...'),
                \Filament\Forms\Components\Select::make('role_id')->relationship('role', 'name')->label('Role')->default(fn () => \App\Models\Role::where('name', 'Anggota')->value('id'))->required()->disabled(fn () => auth()->user()->role?->name !== 'Sekretaris'),
                \Filament\Forms\Components\DatePicker::make('tanggal_bergabung')->label('Tanggal Bergabung')->nullable()->disabled(fn () => auth()->user()->role?->name !== 'Sekretaris'),
                \Filament\Forms\Components\Toggle::make('is_active')->default(true)->label('Status Aktif')->disabled(fn () => auth()->user()->role?->name !== 'Sekretaris'),
                \Filament\Forms\Components\Toggle::make('is_super_admin')->label('Super Admin')->visible(fn() => auth()->user()->is_super_admin),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('nia')->label('NIA')->searchable(),
                \Filament\Tables\Columns\TextColumn::make('name')->label('Nama')->searchable(),
                \Filament\Tables\Columns\TextColumn::make('no_telp')->label('No. Telepon')->searchable(),
                \Filament\Tables\Columns\TextColumn::make('alamat')->label('Alamat')->limit(20)->searchable(),
                \Filament\Tables\Columns\TextColumn::make('tanggal_bergabung')->label('Tgl Bergabung')->date('d M Y')->sortable()->toggleable(isToggledHiddenByDefault: true),
                \Filament\Tables\Columns\TextColumn::make('role.name')->label('Role'),
                \Filament\Tables\Columns\IconColumn::make('is_active')->boolean()->label('Aktif'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()
                    ->modalCancelAction(fn ($action) => $action->color('danger')),
                EditAction::make()
                    ->modalCancelAction(fn ($action) => $action->color('danger')),
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
            'index' => ManageUsers::route('/'),
        ];
    }
}

