<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\CategoryModel;
use App\Models\umkmModel;
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

                        // ✅ KATEGORI
                        Select::make('category_id')
                            ->label('Kategori')
                            ->required()
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
                            ->disabled(
                                fn() => CategoryModel::where('is_active', true)
                                    ->whereNull('parent_id')
                                    ->doesntExist()
                            )
                            ->helperText(
                                fn() => CategoryModel::where('is_active', true)
                                    ->whereNull('parent_id')
                                    ->doesntExist()
                                    ? '⚠️ Belum ada kategori aktif. Tambahkan kategori terlebih dahulu.'
                                    : null
                            )
                            ->rules(['required', 'exists:categories,id'])
                            ->validationMessages([
                                'required' => 'Kategori wajib dipilih.',
                                'exists'   => 'Kategori tidak valid.',
                            ]),

                        // ✅ NAMA PRODUK
                        TextInput::make('name')
                            ->label('Nama Produk')
                            ->required()
                            ->minLength(3)
                            ->maxLength(255)
                            ->rules(['required', 'string', 'min:3', 'max:255'])
                            ->validationMessages([
                                'required' => 'Nama produk wajib diisi.',
                                'min'      => 'Nama produk minimal 3 karakter.',
                                'max'      => 'Nama produk maksimal 255 karakter.',
                            ])
                            ->live()
                            ->afterStateUpdated(function (Set $set, $state) {
                                if ($state) {
                                    $set('slug', Str::slug($state));
                                }
                            }),

                        // ✅ SLUG (auto-generate)
                        TextInput::make('slug')
                            ->label('Slug')
                            ->disabled()
                            ->dehydrated()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->rules(['required', 'string', 'max:255', 'alpha_dash'])
                            ->validationMessages([
                                'required'   => 'Slug wajib diisi.',
                                'unique'     => 'Slug sudah digunakan.',
                                'alpha_dash' => 'Slug hanya boleh huruf, angka, dan tanda hubung.',
                            ]),

                        // ✅ DESKRIPSI
                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->nullable()
                            ->rows(4)
                            ->maxLength(5000)
                            ->rules(['nullable', 'string', 'max:5000'])
                            ->validationMessages([
                                'max' => 'Deskripsi maksimal 5000 karakter.',
                            ]),

                        // ✅ UMKM — hanya tampil untuk ADMIN
                        Select::make('umkm_id')
                            ->label('UMKM')
                            ->visible(fn() => Auth::user()->role === 'admin')
                            ->options(function () {
                                $data = umkmModel::where('verification_status', 'approved')
                                    ->pluck('name', 'id');

                                return $data->isEmpty() ? [] : $data;
                            })
                            ->default(function () {
                                // Owner → otomatis pakai UMKM miliknya
                                if (Auth::user()->role === 'owner') {
                                    return Auth::user()->umkm?->id;
                                }
                                return null;
                            })
                            ->searchable()
                            ->preload()
                            ->placeholder('Pilih UMKM')
                            ->noSearchResultsMessage('UMKM tidak ditemukan.')
                            ->searchPrompt('Ketik untuk mencari UMKM...')
                            ->loadingMessage('Memuat UMKM...')
                            ->helperText(
                                fn() => Auth::user()->role === 'owner'
                                    ? 'Produk otomatis terhubung ke UMKM Anda.'
                                    : null
                            )
                            ->required(fn() => Auth::user()->role === 'admin')
                            ->dehydrated(true)
                            ->rules(function () {
                                return Auth::user()->role === 'admin'
                                    ? ['required', 'exists:umkms,id']
                                    : ['nullable', 'exists:umkms,id'];
                            })
                            ->validationMessages([
                                'required' => 'UMKM wajib dipilih.',
                                'exists'   => 'UMKM tidak valid atau belum diverifikasi.',
                            ]),

                    ])->columns(1),

                // ✅ STATUS
                Section::make('Media & Status')
                    ->schema([

                        Toggle::make('is_active')
                            ->label('Produk Aktif')
                            ->default(true)
                            ->rules(['boolean']),

                    ]),
            ]);
    }
}
