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
                            ->nullable()
                            ->options(function () {
                                $categories = CategoryModel::where('is_active', true)
                                    ->whereNull('parent_id')
                                    ->with('children')
                                    ->get();

                                $options = [];
                                foreach ($categories as $parent) {
                                    if ($parent->children->isNotEmpty()) {
                                        foreach ($parent->children as $child) {
                                            $options[$parent->name][$child->id] = $child->name;
                                        }
                                    } else {
                                        $options[$parent->id] = $parent->name;
                                    }
                                }

                                return $options ?: ['' => 'Kategori tidak ditemukan'];
                            })
                            ->searchable()
                            ->preload()
                            ->disabled(fn() => CategoryModel::where('is_active', true)->count() === 0)
                            ->helperText(
                                fn() => CategoryModel::where('is_active', true)->count() === 0
                                    ? 'Kategori tidak ditemukan'
                                    : null
                            ),

                        TextInput::make('name')
                            ->label('Nama Produk')
                            ->required()
                            ->maxLength(255)
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
                            ->label('Deskripsi')
                            ->nullable()
                            ->rows(4)
                            ->maxLength(5000),

                        Select::make('umkm_id')
                            ->label('UMKM')
                            ->nullable()
                            ->options(function () {
                                $user = Auth::user();

                                if ($user->role === 'owner') {
                                    return umkmModel::where('verification_status', 'approved')
                                        ->where('user_id', $user->id)
                                        ->pluck('name', 'id');
                                }

                                $data = umkmModel::where('verification_status', 'approved')
                                    ->pluck('name', 'id');

                                return $data->isEmpty()
                                    ? ['' => 'UMKM tidak ditemukan']
                                    : $data;
                            })
                            ->default(function () {
                                $user = Auth::user();
                                if ($user->role === 'owner') {
                                    return $user->umkm?->id;
                                }
                                return null;
                            })
                            ->searchable()
                            ->visible(fn() => Auth::user()->role === 'admin')
                            ->required(fn() => Auth::user()->role === 'admin')
                            ->dehydrated(true),

                    ])->columns(1),

                Section::make('Media & Status')
                    ->schema([

                        Toggle::make('is_active')
                            ->label('Produk Aktif')
                            ->default(true),

                        FileUpload::make('images_temp')
                            ->label('Upload Gambar')
                            ->multiple()
                            ->image()
                            ->disk('public')
                            ->directory('temp')
                            ->maxFiles(3)
                            ->maxSize(1024) // 1MB
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->helperText('Format: JPG, PNG, WEBP • Maksimal 1MB per gambar • Maks 3 foto')
                            ->validationMessages([
                                'max'      => 'Ukuran gambar tidak boleh lebih dari 1MB.',
                                'mimes'    => 'Format gambar tidak valid. Gunakan JPG, PNG, atau WEBP.',
                                'maxFiles' => 'Maksimal 3 gambar yang dapat diupload.',
                            ])
                            ->dehydrated(false),

                    ]),
            ]);
    }
}
