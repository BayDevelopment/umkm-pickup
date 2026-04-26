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
                            ->required() // ✅ wajib
                            ->options(function () {
                                $categories = CategoryModel::where('is_active', true)
                                    ->whereNull('parent_id')
                                    ->pluck('name', 'id');

                                return $categories->isNotEmpty() ? $categories : [];
                            })
                            ->searchable()
                            ->preload()
                            ->placeholder('Pilih Kategori')
                            ->noSearchResultsMessage('Kategori tidak ditemukan.')
                            ->searchPrompt('Ketik untuk mencari kategori...')
                            ->loadingMessage('Memuat kategori...')
                            ->disabled(fn() => CategoryModel::where('is_active', true)->whereNull('parent_id')->exists() === false)
                            ->helperText(
                                fn() => CategoryModel::where('is_active', true)->whereNull('parent_id')->exists() === false
                                    ? '⚠️ Belum ada kategori aktif. Tambahkan kategori terlebih dahulu.'
                                    : null
                            )
                            ->rules([
                                'required', // ✅ fix: sesuai dengan ->required()
                                'exists:categories,id',
                            ]),

                        TextInput::make('name')
                            ->label('Nama Produk')
                            ->required()
                            ->maxLength(255)
                            ->rules([
                                'required',
                                'string',
                                'max:255',
                            ])
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
                            ->maxLength(5000)
                            ->rules([
                                'nullable',
                                'string',
                                'max:5000',
                            ]),

                        Select::make('umkm_id')
                            ->label('UMKM')
                            ->options(function () {
                                $user = Auth::user();

                                if ($user->role === 'owner') {
                                    return umkmModel::where('verification_status', 'approved')
                                        ->where('user_id', $user->id)
                                        ->pluck('name', 'id');
                                }

                                $data = umkmModel::where('verification_status', 'approved')
                                    ->pluck('name', 'id');

                                return $data->isEmpty() ? [] : $data;
                            })
                            ->default(function () {
                                $user = Auth::user();
                                if ($user->role === 'owner') {
                                    return $user->umkm?->id; // ← pastikan relasi umkm ada di User model
                                }
                                return null;
                            })
                            ->searchable()
                            ->visible(fn() => Auth::user()->role === 'admin')
                            ->required(fn() => Auth::user()->role === 'admin')
                            ->dehydrated(true) // ✅ wajib ada agar value tetap terkirim meski hidden
                            ->rules([
                                'required',
                                'exists:umkms,id',
                            ]),

                    ])->columns(1),

                Section::make('Media & Status')
                    ->schema([

                        Toggle::make('is_active')
                            ->label('Produk Aktif')
                            ->default(true)
                            ->rules(['boolean']),

                        FileUpload::make('images_temp')
                            ->label('Upload Gambar')
                            ->multiple()
                            ->image()
                            ->disk('public')        // ← disk untuk final storage
                            ->directory('temp')
                            ->maxFiles(3)
                            ->maxSize(1024)
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
