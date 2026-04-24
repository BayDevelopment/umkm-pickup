<?php

namespace App\Filament\Resources\Categories;

use App\Filament\Resources\Categories\Pages\CreateCategories;
use App\Filament\Resources\Categories\Pages\EditCategories;
use App\Filament\Resources\Categories\Pages\ListCategories;
use App\Filament\Resources\Categories\Schemas\CategoriesForm;
use App\Filament\Resources\Categories\Tables\CategoriesTable;
use App\Models\Categories;
use App\Models\CategoryModel;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class CategoriesResource extends Resource
{
    protected static ?string $model = CategoryModel::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-folder';

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
        return 'Category';
    }
    public static function getPluralModelLabel(): string
    {
        return 'Data Category';
    }
    protected static ?string $navigationLabel = 'Category';
    protected static ?int    $navigationSort  = 2;

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
        return CategoriesForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CategoriesTable::configure($table);
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
            'index' => ListCategories::route('/'),
            'create' => CreateCategories::route('/create'),
            'edit' => EditCategories::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
