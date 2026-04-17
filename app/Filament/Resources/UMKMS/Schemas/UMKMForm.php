<?php

namespace App\Filament\Resources\UMKMS\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class UMKMForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // 🔹 DATA UMKM
                Section::make('Informasi UMKM')
                    ->description('Data dasar usaha')
                    ->icon('heroicon-o-building-storefront')
                    ->schema([

                        TextInput::make('name')
                            ->label('Nama UMKM')
                            ->placeholder('Contoh : Dapur Nyiroro')
                            ->required()
                            ->minLength(3)
                            ->maxLength(255)
                            ->regex('/^[a-zA-Z0-9\s]+$/') // hanya huruf, angka, spasi
                            ->validationMessages([
                                'regex' => 'Nama hanya boleh huruf dan angka',
                            ]),

                        TextInput::make('city')
                            ->label('Kota')
                            ->placeholder('Contoh : Cilegon')
                            ->maxLength(100)
                            ->regex('/^[a-zA-Z\s]+$/')
                            ->validationMessages([
                                'regex' => 'Kota hanya boleh huruf',
                            ]),

                        Textarea::make('address')
                            ->label('Alamat')
                            ->placeholder('Contoh : Jl.Saturnus, Kec, Kel, Kota')
                            ->rows(3)
                            ->required()
                            ->minLength(10)
                            ->columnSpanFull(),

                    ]),

                // 🔹 DATA VERIFIKASI
                Section::make('Verifikasi Identitas')
                    ->description('Digunakan untuk validasi UMKM')
                    ->icon('heroicon-o-identification')
                    ->schema([

                        Select::make('user_id')
                            ->label('Owner')
                            ->options(function () {
                                $owners = \App\Models\User::where('role', 'owner')
                                    ->pluck('name', 'id');

                                // kalau kosong → tampilkan pesan
                                return $owners->isEmpty()
                                    ? ['' => 'Pengguna dengan role owner kosong']
                                    : $owners;
                            })
                            ->default(
                                fn() =>
                                Auth::user()->role === 'owner'
                                    ? Auth::id()
                                    : null
                            )
                            ->disabled(function () {
                                $isOwnerEmpty = \App\Models\User::where('role', 'owner')->count() === 0;

                                return $isOwnerEmpty || Auth::user()->role !== 'admin';
                            })
                            ->required()
                            ->searchable()
                            ->helperText(function () {
                                if (\App\Models\User::where('role', 'owner')->count() === 0) {
                                    return 'Pengguna dengan role owner kosong';
                                }

                                if (Auth::user()->role !== 'admin') {
                                    return 'UMKM ini otomatis milik Anda';
                                }

                                return null;
                            }),

                        TextInput::make('ktp_number')
                            ->label('NIK')
                            ->placeholder('Contoh : 320xxxxxxxxxxxxx')
                            ->required()
                            ->length(16) // 🔥 wajib 16 digit
                            ->regex('/^[0-9]+$/')
                            ->unique(ignoreRecord: true)
                            ->validationMessages([
                                'length' => 'NIK harus 16 digit',
                                'regex' => 'NIK hanya boleh angka',
                            ]),

                        FileUpload::make('ktp_image')
                            ->label('Foto KTP')
                            ->image()
                            ->disk('public')
                            ->directory('ktp')
                            ->acceptedFileTypes(['image/jpeg', 'image/png'])
                            ->maxSize(2048) // 2MB
                            ->required()
                            ->imageEditor() // opsional (biar bisa crop)
                            ->validationMessages([
                                'required' => 'Foto KTP wajib diupload',
                            ]),

                    ]),

                // 🔹 STATUS (ADMIN ONLY 🔐)
                Section::make('Status Verifikasi')
                    ->description('Kontrol oleh admin')
                    ->icon('heroicon-o-shield-check')
                    ->visible(fn() => Auth::user()->role === 'admin')
                    ->schema([

                        Select::make('verification_status')
                            ->label('Status')
                            ->options([
                                'pending' => 'Pending',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                            ])
                            ->required(),

                        Textarea::make('verification_note')
                            ->label('Catatan Admin')
                            ->rows(3)
                            ->placeholder('Isi jika ditolak...')
                            ->visible(fn($get) => $get('verification_status') === 'rejected'),

                    ]),

            ]);
    }
}
