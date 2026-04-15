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
                    ->description('Detail utama produk')
                    ->icon('heroicon-o-cube')
                    ->schema([

                        Select::make('category_id')
                            ->label('Kategori')
                            ->relationship(
                                name: 'category',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn($query) => $query->where('is_active', true)
                            )
                            ->searchable()
                            ->preload()
                            ->required(fn() => CategoryModel::where('is_active', true)->exists())
                            ->disabled(fn() => !CategoryModel::where('is_active', true)->exists())
                            ->helperText(function () {
                                return !CategoryModel::where('is_active', true)->exists()
                                    ? 'Kategori tidak ditemukan!'
                                    : null;
                            }),

                        // 🔥 TYPE (WAJIB)
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
                            ->minLength(3)
                            ->maxLength(255)
                            ->live()
                            ->afterStateUpdated(function (Set $set, ?string $state) {
                                if ($state) {
                                    $slug = Str::slug($state);

                                    // 🔥 OPTIONAL: prefix umkm biar unik
                                    if (Auth::user()->role === 'owner') {
                                        $slug = Auth::user()->umkm_id . '-' . $slug;
                                    }

                                    $set('slug', $slug);
                                }
                            }),

                        TextInput::make('slug')
                            ->disabled()
                            ->dehydrated()
                            ->required()
                            ->unique(ignoreRecord: true),

                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->columns(1),

                Section::make('Media & Status')
                    ->description('Gambar dan status publikasi')
                    ->icon('heroicon-o-photo')
                    ->schema([

                        FileUpload::make('image')
                            ->label('Gambar Produk')
                            ->image()
                            ->multiple()
                            ->disk('public')
                            ->directory('products')
                            ->reorderable()
                            ->maxFiles(3)
                            ->acceptedFileTypes(['image/jpeg', 'image/png'])
                            ->maxSize(2048)
                            ->saveUploadedFileUsing(function ($file) {

                                $manager = new ImageManager(new Driver());

                                $image = $manager->read($file->getRealPath());

                                $image->scale(height: 480);

                                $filename = Str::uuid() . '.jpg';

                                Storage::disk('public')->put(
                                    'products/' . $filename,
                                    $image->toJpeg(85)
                                );

                                return 'products/' . $filename;
                            })
                            ->helperText('Gambar akan otomatis resize tinggi 480px'),

                        Toggle::make('is_active')
                            ->label('Aktifkan Produk')
                            ->default(true),

                        Select::make('umkm_id')
                            ->label('UMKM')
                            ->relationship(
                                name: 'umkm',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn($query) =>
                                $query->where('verification_status', 'approved')
                            )
                            ->visible(fn() => Auth::user()->role === 'admin')

                            ->required(
                                fn() =>
                                Auth::user()->role === 'admin' &&
                                    umkmModel::where('verification_status', 'approved')->exists()
                            )

                            ->disabled(
                                fn() =>
                                !umkmModel::where('verification_status', 'approved')->exists()
                            )

                            ->helperText(
                                fn() =>
                                !umkmModel::where('verification_status', 'approved')->exists()
                                    ? 'UMKM tidak ditemukan! Silakan approve UMKM terlebih dahulu.'
                                    : null
                            )
                    ])
            ]);
    }
}
