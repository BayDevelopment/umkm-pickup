<?php

namespace App\Filament\Resources\Branches;

use App\Filament\Resources\Branches\Pages\CreateBranch;
use App\Filament\Resources\Branches\Pages\EditBranch;
use App\Filament\Resources\Branches\Pages\ListBranches;
use App\Filament\Resources\Branches\Schemas\BranchForm;
use App\Filament\Resources\Branches\Tables\BranchesTable;
use App\Models\Branch;
use App\Models\BranchModel;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class BranchResource extends Resource
{
    protected static ?string $model = BranchModel::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $recordTitleAttribute = 'name';

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
        return 'Cabang';
    }
    public static function getPluralModelLabel(): string
    {
        return 'Data Cabang';
    }
    protected static ?string $navigationLabel = 'Cabang';
    protected static ?int    $navigationSort  = 1;

    public static function canAccess(): bool
    {
        return Auth::user()->role === 'admin';
    }
    public static function canEdit($record): bool
    {
        return Auth::user()->role === 'admin';
    }
    public static function canCreate(): bool
    {
        return Auth::user()->role === 'admin';
    }
    public static function canDelete($record): bool
    {
        return Auth::user()->role === 'admin';
    }
    public static function canDeleteAny(): bool
    {
        return Auth::user()->role === 'admin';
    }

    public static function form(Schema $schema): Schema
    {
        return BranchForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BranchesTable::configure($table);
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
            'index' => ListBranches::route('/'),
            'create' => CreateBranch::route('/create'),
            'edit' => EditBranch::route('/{record}/edit'),
        ];
    }
}
