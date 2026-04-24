<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\CategoryModel;
use App\Models\umkmModel;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
// intervention image
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Storage;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Informasi Produk')
                    ->schema([

                        Select::make('category_id')
                            ->label('Kategori')
                            ->options(function () {
                                $data = CategoryModel::where('is_active', true)->pluck('name', 'id');

                                return $data->isEmpty()
                                    ? ['' => 'Kategori tidak ditemukan']
                                    : $data;
                            })
                            ->searchable()
                            ->preload()
                            ->disabled(fn() => CategoryModel::where('is_active', true)->count() === 0)
                            ->helperText(
                                fn() =>
                                CategoryModel::where('is_active', true)->count() === 0
                                    ? 'Kategori tidak ditemukan'
                                    : null
                            ),

                        Select::make('type')
                            ->label('Jenis Produk')
                            ->options([
                                'food' => 'Makanan',
                                'drink' => 'Minuman',
                                'fashion' => 'Fashion',
                            ])
                            ->required(),

                        TextInput::make('name')
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Set $set, $state) {
                                if ($state) {
                                    $set('slug', Str::slug($state));
                                }
                            }),

                        TextInput::make('slug')
                            ->disabled()
                            ->dehydrated()
                            ->required()
                            ->unique(ignoreRecord: true),

                        Textarea::make('description')
                            ->rows(4),

                        // 🔥 AUTO UMKM UNTUK OWNER
                        Select::make('umkm_id')
                            ->label('UMKM')
                            ->options(function () {
                                $user = Auth::user();

                                // Owner hanya lihat UMKM miliknya
                                if ($user->role === 'owner') {
                                    return umkmModel::where('verification_status', 'approved')
                                        ->where('user_id', $user->id)
                                        ->pluck('name', 'id');
                                }

                                // Admin lihat semua
                                $data = umkmModel::where('verification_status', 'approved')
                                    ->pluck('name', 'id');

                                return $data->isEmpty()
                                    ? ['' => 'UMKM tidak ditemukan']
                                    : $data;
                            })
                            ->default(function () {
                                $user = Auth::user();
                                if ($user->role === 'owner') {
                                    return $user->umkm?->id; // otomatis isi umkm_id milik owner
                                }
                                return null;
                            })
                            ->searchable()
                            ->visible(fn() => Auth::user()->role === 'admin')
                            ->required(fn() => Auth::user()->role === 'admin')
                            ->dehydrated(true), // pastikan nilai terkirim meski hidden
                    ])
                    ->columns(1),

                Section::make('Media & Status')
                    ->schema([

                        Toggle::make('is_active')
                            ->default(true),

                        // ❗ HANYA UPLOAD, PENYIMPANAN DI HANDLE DI RESOURCE
                        FileUpload::make('images_temp')
                            ->label('Upload Gambar')
                            ->multiple()
                            ->image()
                            ->disk('public')
                            ->directory('temp')
                            ->maxFiles(3)
                            ->dehydrated(false),

                    ])
            ]);
    }
}
