<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\CategoryModel;

class CategoriesForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Category Information')
                    ->schema([

                        // ✅ NAME
                        TextInput::make('name')
                            ->required()
                            ->minLength(3)
                            ->maxLength(255)
                            ->rules([
                                'required',
                                'string',
                                'min:3',
                                'max:255',
                            ])
                            ->live() // realtime agar parent_id langsung aktif
                            ->afterStateUpdated(function ($state, callable $set) {
                                $slug = Str::slug($state);
                                $slug = preg_replace('/[0-9]/', '', $slug);
                                $set('slug', $slug);
                            }),

                        // ✅ SLUG
                        TextInput::make('slug')
                            ->disabled()
                            ->dehydrated()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->rules([
                                'required',
                                'string',
                                'max:255',
                                'alpha_dash',
                            ]),

                        // ✅ PARENT (HIERARCHY)
                        Select::make('parent_id')
                            ->label('Parent Category')
                            ->relationship(
                                name: 'parent',
                                titleAttribute: 'name',
                                modifyQueryUsing: function ($query, $record) { // ← di sini
                                    $query->whereNull('parent_id');

                                    if ($record?->id) {
                                        $query->where('id', '!=', $record->id);
                                    }
                                }
                            )
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->placeholder('No Parent (Main Category)')
                            ->noSearchResultsMessage('Data tidak ditemukan.')
                            ->searchPrompt('Ketik untuk mencari kategori...')
                            ->loadingMessage('Memuat kategori...')
                            ->disabled(fn($get) => strlen(trim($get('name') ?? '')) < 3)
                            ->dehydrated()
                            ->rules([
                                'nullable',
                                'exists:categories,id',
                            ])
                            ->rule(function ($record) {
                                return function (string $attribute, $value, $fail) use ($record) {
                                    if (blank($value)) return;

                                    if ($record?->id && (int) $value === $record->id) {
                                        $fail('Kategori tidak boleh menjadi parent dirinya sendiri.');
                                    }

                                    if (!CategoryModel::where('id', $value)->exists()) {
                                        $fail('Parent category tidak valid.');
                                    }
                                };
                            }),

                        // ✅ IMAGE
                        FileUpload::make('image')
                            ->image()
                            ->directory('categories')
                            ->nullable()
                            ->maxSize(1024) // 1MB
                            ->rules([
                                'nullable',
                                'image',
                                'mimes:jpg,jpeg,png,webp',
                                'max:1024',
                            ]),

                        // ✅ STATUS
                        Toggle::make('is_active')
                            ->default(true)
                            ->rules(['boolean']),

                    ])
                    ->columns(1),
            ]);
    }
}
