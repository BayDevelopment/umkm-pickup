<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([

                // 🔒 order_code tidak ditampilkan & tidak dikirim dari client
                Hidden::make('order_code')
                    ->dehydrated(false),

                Section::make('Informasi Order')
                    ->schema([

                        Select::make('user_id')
                            ->label('Customer')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->required()
                            ->validationMessages([
                                'required' => 'Customer wajib dipilih.',
                            ]),

                        Select::make('payment_method_id')
                            ->label('Metode Pembayaran')
                            ->relationship(
                                'paymentMethod',
                                'name',
                                fn($q) => $q->where('is_active', true)
                            )
                            ->searchable()
                            ->validationMessages([
                                'required' => 'Metode pembayaran wajib dipilih.',
                            ]),

                        Grid::make(2)->schema([

                            Select::make('status')
                                ->options([
                                    'pending' => 'Pending',
                                    'process' => 'Process',
                                    'done'    => 'Done',
                                    'cancel'  => 'Cancel',
                                ])
                                ->required()
                                ->validationMessages([
                                    'required' => 'Status order wajib dipilih.',
                                ]),

                            Select::make('payment_status')
                                ->options([
                                    'pending'  => 'Pending',
                                    'paid'     => 'Paid',
                                    'rejected' => 'Rejected',
                                ])
                                ->required()
                                ->validationMessages([
                                    'required' => 'Status pembayaran wajib dipilih.',
                                ]),

                        ]),
                    ])
                    ->columnSpanFull(),

                Section::make('Items Pesanan')
                    ->schema([

                        Repeater::make('items')
                            ->relationship()
                            ->minItems(1)
                            ->validationMessages([
                                'min_items' => 'Minimal harus ada 1 item.',
                            ])
                            ->schema([

                                Select::make('product_variant_id')
                                    ->label('Product Variant')
                                    ->relationship('variant', 'sku')
                                    ->preload() // 🔥 tampil langsung
                                    ->searchable() // 🔥 tetap bisa search
                                    ->required()
                                    ->validationMessages([
                                        'required' => 'Variant wajib dipilih.',
                                    ]),

                                TextInput::make('quantity')
                                    ->numeric()
                                    ->minValue(1)
                                    ->required()
                                    ->validationMessages([
                                        'required' => 'Jumlah wajib diisi.',
                                        'numeric'  => 'Jumlah harus berupa angka.',
                                        'min'      => 'Jumlah minimal 1.',
                                    ]),

                                // 🔒 price tidak boleh dipercaya dari client
                                TextInput::make('price')
                                    ->numeric()
                                    ->minValue(0)
                                    ->required()
                                    ->dehydrated(false) // tidak dikirim
                                    ->validationMessages([
                                        'required' => 'Harga wajib diisi.',
                                        'numeric'  => 'Harga harus berupa angka.',
                                        'min'      => 'Harga tidak boleh negatif.',
                                    ]),

                                // 🔒 subtotal hanya tampilan
                                TextInput::make('subtotal')
                                    ->numeric()
                                    ->disabled()
                                    ->dehydrated(false) // 🔥 tidak dikirim ke server

                            ])
                            ->columns(4)
                            ->createItemButtonLabel('Tambah Item'),

                    ])
                    ->columnSpanFull(),

                Section::make('Total')
                    ->schema([
                        TextInput::make('total_price')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(false), // dihitung di backend
                    ])
                    ->columnSpanFull(),

            ]);
    }
}
