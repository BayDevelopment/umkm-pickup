<?php

namespace App\Filament\Resources\PaymentMethods\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PaymentMethodForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Informasi Metode Pembayaran')
                    ->description('Data rekening tujuan yang akan ditampilkan kepada customer saat checkout.')
                    ->icon('heroicon-o-credit-card')
                    ->columnSpanFull()
                    ->columns(1)
                    ->schema([

                        TextInput::make('name')
                            ->label('Nama Metode')
                            ->placeholder('Contoh: Transfer Bank')
                            ->prefixIcon('heroicon-o-wallet')
                            ->required()
                            ->maxLength(255)
                            ->validationMessages([
                                'required' => 'Nama metode pembayaran wajib diisi.',
                                'max' => 'Nama metode maksimal 255 karakter.',
                            ])
                            ->columnSpan(2),

                        TextInput::make('bank_name')
                            ->label('Nama Bank')
                            ->placeholder('Contoh: BCA, BRI, Mandiri')
                            ->prefixIcon('heroicon-o-building-library')
                            ->maxLength(255)
                            ->validationMessages([
                                'max' => 'Nama bank maksimal 255 karakter.',
                            ]),

                        TextInput::make('account_number')
                            ->label('Nomor Rekening')
                            ->placeholder('Contoh: 1234567890')
                            ->prefixIcon('heroicon-o-hashtag')
                            ->numeric()
                            ->required()
                            ->maxLength(30)
                            ->unique(ignoreRecord: true)
                            ->validationMessages([
                                'required' => 'Nomor rekening wajib diisi.',
                                'numeric' => 'Nomor rekening hanya boleh angka.',
                                'max' => 'Nomor rekening maksimal 30 digit.',
                                'unique' => 'Nomor rekening sudah digunakan.',
                            ]),

                        TextInput::make('account_name')
                            ->label('Atas Nama')
                            ->placeholder('Contoh: PT Trendora')
                            ->prefixIcon('heroicon-o-user')
                            ->required()
                            ->maxLength(255)
                            ->validationMessages([
                                'required' => 'Nama pemilik rekening wajib diisi.',
                                'max' => 'Nama pemilik maksimal 255 karakter.',
                            ]),

                        Toggle::make('is_active')
                            ->label('Aktifkan Metode Pembayaran')
                            ->helperText('Jika nonaktif, metode ini tidak akan muncul di halaman checkout.')
                            ->onIcon('heroicon-o-check-circle')
                            ->offIcon('heroicon-o-x-circle')
                            ->default(true)
                            ->columnSpan(2),

                    ]),
            ]);
    }
}
