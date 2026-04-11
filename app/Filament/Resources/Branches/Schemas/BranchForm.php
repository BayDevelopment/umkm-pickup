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
                ->description('Detail utama cabang')
                ->icon('heroicon-o-building-storefront')
                ->schema([

                    TextInput::make('name')
                        ->label('Nama Cabang')
                        ->placeholder('Contoh: Cabang Bandung Dago')
                        ->required()
                        ->maxLength(255)
                        ->live(debounce: 500)
                        ->afterStateUpdated(function (?string $state, Set $set) {
                            if ($state) {
                                $set('slug', Str::slug($state));
                            }
                        })
                        ->validationMessages([
                            'required' => 'Nama cabang wajib diisi ya!',
                        ]),

                    TextInput::make('slug')
                        ->label('Slug')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255)
                        ->helperText('Otomatis dibuat dari nama cabang, tapi bisa diedit manual.')
                        ->validationMessages([
                            'required' => 'Slug wajib diisi',
                            'unique' => 'Slug sudah digunakan cabang lain',
                        ]),

                    Toggle::make('is_active')
                        ->label('Cabang Aktif')
                        ->default(true)
                        ->inline(false),

                ])
                ->columns(2),


            /*
            |--------------------------------------------------------------------------
            | Lokasi
            |--------------------------------------------------------------------------
            */
            Section::make('Lokasi')
                ->description('Alamat lengkap cabang')
                ->icon('heroicon-o-map-pin')
                ->schema([

                    Textarea::make('address')
                        ->label('Alamat Lengkap')
                        ->rows(3)
                        ->columnSpanFull()
                        ->required()
                        ->placeholder('Jl. Sudirman No. 45, RT.003/RW.002')
                        ->validationMessages([
                            'required' => 'Alamat wajib diisi',
                        ]),

                    TextInput::make('city')
                        ->label('Kota / Kabupaten')
                        ->required()
                        ->placeholder('Jakarta Selatan'),

                    TextInput::make('district')
                        ->label('Kecamatan')
                        ->placeholder('Setiabudi'),

                    TextInput::make('subdistrict')
                        ->label('Kelurahan')
                        ->placeholder('Karet Kuningan'),

                    TextInput::make('postal_code')
                        ->label('Kode Pos')
                        ->maxLength(10)
                        ->placeholder('12950'),

                ])
                ->columns(2),


            /*
            |--------------------------------------------------------------------------
            | Koordinat & Operasional
            |--------------------------------------------------------------------------
            */
            Section::make('Koordinat & Operasional')
                ->description('Lokasi GPS dan jam operasional cabang')
                ->icon('heroicon-o-clock')
                ->schema([

                    Grid::make(2)
                        ->schema([

                            TextInput::make('latitude')
                                ->label('Latitude')
                                ->numeric()
                                ->step('any') // supaya tidak error step
                                ->minValue(-90)
                                ->maxValue(90)
                                ->placeholder('-6.208800')
                                ->helperText('Contoh: -6.208800 (Range: -90 sampai 90)')

                                // auto format ke 6 desimal saat load
                                ->formatStateUsing(
                                    fn($state) =>
                                    $state !== null ? number_format((float) $state, 6, '.', '') : null
                                )

                                // auto format saat user input
                                ->afterStateUpdated(
                                    fn($state, callable $set) =>
                                    $state !== null
                                        ? $set('latitude', number_format((float) $state, 6, '.', ''))
                                        : null
                                )

                                ->validationMessages([
                                    'numeric' => 'Latitude harus berupa angka',
                                    'minValue' => 'Latitude minimal -90',
                                    'maxValue' => 'Latitude maksimal 90',
                                ]),


                            TextInput::make('longitude')
                                ->label('Longitude')
                                ->numeric()
                                ->step('any')
                                ->minValue(-180)
                                ->maxValue(180)
                                ->placeholder('106.845600')
                                ->helperText('Contoh: 106.845600 (Range: -180 sampai 180)')

                                ->formatStateUsing(
                                    fn($state) =>
                                    $state !== null ? number_format((float) $state, 6, '.', '') : null
                                )

                                ->afterStateUpdated(
                                    fn($state, callable $set) =>
                                    $state !== null
                                        ? $set('longitude', number_format((float) $state, 6, '.', ''))
                                        : null
                                )

                                ->validationMessages([
                                    'numeric' => 'Longitude harus berupa angka',
                                    'minValue' => 'Longitude minimal -180',
                                    'maxValue' => 'Longitude maksimal 180',
                                ]),

                        ]),

                    Grid::make(2)
                        ->schema([

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
                        ->placeholder('0812-3456-7890'),

                ]),


            /*
            |--------------------------------------------------------------------------
            | Foto Cabang
            |--------------------------------------------------------------------------
            */
            Section::make('Foto Cabang')
                ->description('Upload hanya JPG, maksimal 1MB')
                ->icon('heroicon-o-photo')
                ->schema([

                    FileUpload::make('image')
                        ->label('Foto Cabang')
                        ->image()
                        ->acceptedFileTypes(['image/jpeg', 'image/jpg'])
                        ->maxSize(1024)
                        ->directory('branches')
                        ->imageResizeTargetWidth(800)
                        ->imageResizeTargetHeight(600)
                        ->nullable()
                        ->helperText('Format: JPG saja, maksimal 1MB'),

                ]),

        ]);
    }
}
