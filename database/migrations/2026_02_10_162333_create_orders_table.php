<?php

namespace App\Filament\Resources\Orders\Schemas;

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

                Section::make('Informasi Order')
                    ->schema([

                        TextInput::make('order_code')
                            ->label('Kode Order')
                            ->disabled() // biasanya auto generate
                            ->dehydrated(),

                        Select::make('user_id')
                            ->label('Customer')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->required(),

                        Select::make('branch_id')
                            ->label('Cabang')
                            ->relationship('branch', 'name')
                            ->searchable(),

                        Select::make('payment_method_id')
                            ->label('Metode Pembayaran')
                            ->relationship(
                                'paymentMethod',
                                'name',
                                fn($query) => $query->where('is_active', true)
                            )
                            ->searchable(),

                        Grid::make(2)
                            ->schema([

                                Select::make('status')
                                    ->options([
                                        'pending' => 'Pending',
                                        'process' => 'Process',
                                        'done'    => 'Done',
                                        'cancel'  => 'Cancel',
                                    ])
                                    ->required(),

                                Select::make('payment_status')
                                    ->options([
                                        'pending'  => 'Pending',
                                        'paid'     => 'Paid',
                                        'rejected' => 'Rejected',
                                    ])
                                    ->required(),

                            ]),

                    ])
                    ->columnSpanFull(),

                Section::make('Snapshot Pembayaran')
                    ->schema([

                        TextInput::make('bank_name')
                            ->label('Nama Bank')
                            ->maxLength(255),

                        TextInput::make('bank_account_number')
                            ->label('Nomor Rekening')
                            ->numeric(),

                        TextInput::make('bank_account_name')
                            ->label('Atas Nama'),

                    ])
                    ->columnSpanFull(),

                Section::make('Items Pesanan')
                    ->schema([

                        Repeater::make('items')
                            ->relationship()
                            ->schema([

                                Select::make('product_variant_id')
                                    ->label('Product Variant')
                                    ->relationship('variant', 'sku')
                                    ->searchable()
                                    ->required(),

                                TextInput::make('quantity')
                                    ->numeric()
                                    ->minValue(1)
                                    ->required(),

                                TextInput::make('price')
                                    ->numeric()
                                    ->minValue(0)
                                    ->required(),

                                TextInput::make('subtotal')
                                    ->numeric()
                                    ->disabled(),

                            ])
                            ->columns(4)
                            ->createItemButtonLabel('Tambah Item'),

                    ])
                    ->columnSpanFull(),

                Section::make('Total')
                    ->schema([

                        TextInput::make('total_price')
                            ->numeric()
                            ->minValue(0)
                            ->disabled(),

                    ])
                    ->columnSpanFull(),

            ]);
    }
}
