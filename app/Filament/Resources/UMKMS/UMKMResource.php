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
