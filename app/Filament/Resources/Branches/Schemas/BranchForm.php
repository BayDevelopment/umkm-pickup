<?php

namespace App\Filament\Resources\Branches\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class BranchForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([

            /*
        |--------------------------------------------------------------------------
        | Informasi Dasar
        |--------------------------------------------------------------------------
        */
            Section::make('Informasi Dasar')
                ->schema([

                    TextInput::make('name')
                        ->label('Nama Cabang')
                        ->required()
                        ->maxLength(255)
                        ->live(debounce: 500)
                        ->afterStateUpdated(function (?string $state, Set $set) {
                            if ($state) {
                                $set('slug', Str::slug($state));
                            }
                        }),

                    TextInput::make('slug')
                        ->label('Slug')
                        ->disabled()
                        ->dehydrated()
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),

                    Toggle::make('is_active')
                        ->label('Cabang Aktif')
                        ->default(true),

                ])
                ->columns(1),

            /*
        |--------------------------------------------------------------------------
        | Lokasi
        |--------------------------------------------------------------------------
        */
            Section::make('Lokasi')
                ->schema([

                    Textarea::make('address')
                        ->label('Alamat Lengkap')
                        ->required()
                        ->rows(3)
                        ->columnSpanFull(),

                    TextInput::make('city')
                        ->label('Kota / Kabupaten')
                        ->required(),

                    TextInput::make('district')
                        ->label('Kecamatan'),

                    TextInput::make('subdistrict')
                        ->label('Kelurahan'),

                    TextInput::make('postal_code')
                        ->label('Kode Pos')
                        ->maxLength(10),

                ])
                ->columns(1),

            /*
        |--------------------------------------------------------------------------
        | Koordinat & Operasional
        |--------------------------------------------------------------------------
        */
            Section::make('Koordinat & Operasional')
                ->schema([

                    Grid::make(2)->schema([

                        TextInput::make('latitude')
                            ->label('Latitude')
                            ->numeric()
                            ->step('any')
                            ->minValue(-90)
                            ->maxValue(90)
                            ->nullable(),

                        TextInput::make('longitude')
                            ->label('Longitude')
                            ->numeric()
                            ->step('any')
                            ->minValue(-180)
                            ->maxValue(180)
                            ->nullable(),

                    ]),

                    Grid::make(2)->schema([

                        TimePicker::make('opening_time')
                            ->label('Jam Buka')
                            ->seconds(false),

                        TimePicker::make('closing_time')
                            ->label('Jam Tutup')
                            ->seconds(false),

                    ]),

                    TextInput::make('phone')
                        ->label('Nomor Telepon')
                        ->tel()
                        ->maxLength(20)
                        ->rules(['nullable', 'regex:/^[0-9+\-() ]+$/']),

                ]),

            /*
        |--------------------------------------------------------------------------
        | Foto Cabang
        |--------------------------------------------------------------------------
        */
            Section::make('Foto Cabang')
                ->schema([

                    FileUpload::make('image')
                        ->label('Foto Cabang')
                        ->image()
                        ->disk('public') // 🔥 penting
                        ->directory('branches')
                        ->acceptedFileTypes(['image/jpeg'])
                        ->maxSize(1024)
                        ->nullable(),

                ]),

        ]);
    }
}
