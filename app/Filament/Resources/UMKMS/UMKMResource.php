<?php

namespace App\Filament\Resources\UMKMS;

use App\Filament\Resources\UMKMS\Pages\CreateUMKM;
use App\Filament\Resources\UMKMS\Pages\EditUMKM;
use App\Filament\Resources\UMKMS\Pages\ListUMKMS;
use App\Filament\Resources\UMKMS\Schemas\UMKMForm;
use App\Filament\Resources\UMKMS\Tables\UMKMSTable;
use App\Models\UMKM;
use App\Models\umkmModel;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class UMKMResource extends Resource
{
    protected static ?string $model = umkmModel::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArchiveBox;

    protected static ?string $recordTitleAttribute = 'name';

    // ADD
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    public static function getNavigationGroup(): ?string
    {
        return 'Master Data';
    }
    public static function getModelLabel(): string
    {
        return 'UMKM';
    }
    public static function getPluralModelLabel(): string
    {
        return 'Data UMKM';
    }
    protected static ?string $navigationLabel = 'UMKM';
    protected static ?int    $navigationSort  = 3;

    public static function canAccess(): bool
    {
        $user = Auth::user();

        if ($user->role === 'owner' && $user->status === 'pending' && !$user->umkm) {
            return true;
        }

        return in_array($user->role, ['admin', 'owner']);
    }

    public static function canCreate(): bool
    {
        $user = Auth::user();

        // Owner yang belum punya UMKM boleh create
        if ($user->role === 'owner' && !$user->umkm) {
            return true;
        }

        return $user->role === 'admin';
    }

    public static function canView($record): bool
    {
        $user = Auth::user();

        if ($user->role === 'owner') {
            // Hanya bisa lihat jika milik sendiri DAN sudah approved
            return $record->user_id === $user->id
                && $record->verification_status === 'approved';
        }

        return $user->role === 'admin';
    }

    public static function canEdit($record): bool
    {
        $user = Auth::user();

        if ($user->role === 'owner') {
            // Hanya bisa edit jika milik sendiri DAN status masih pending
            return $record->user_id === $user->id
                && $record->verification_status === 'pending';
        }

        return $user->role === 'admin';
    }

    public static function canDelete($record): bool
    {
        return Auth::user()->role === 'admin';
    }

    public static function canDeleteAny(): bool
    {
        return Auth::user()->role === 'admin';
    }


    // LAST ADD

    public static function form(Schema $schema): Schema
    {
        return UMKMForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UMKMSTable::configure($table);
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
            'index' => ListUMKMS::route('/'),
            'create' => CreateUMKM::route('/create'),
            'edit' => EditUMKM::route('/{record}/edit'),
        ];
    }
}
