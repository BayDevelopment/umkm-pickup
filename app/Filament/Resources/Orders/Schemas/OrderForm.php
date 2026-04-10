<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Models\User;
use App\Models\PayMethodModel;
use App\Models\ProductVariantModel;
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
            ->columns(1) // 🔥 bikin semua section full width
            ->components([

                Section::make('Informasi Order')
                    ->schema([

                        Select::make('user_id')
                            ->label('Customer')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->required(),

                        Select::make('payment_method_id')
                            ->label('Metode Pembayaran')
                            ->relationship('paymentMethod', 'name')
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
                    ->columnSpanFull(), // 🔥 optional biar makin tegas full

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
                                    ->required()
                                    ->minValue(1),

                                TextInput::make('price')
                                    ->numeric()
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
                            ->disabled(),
                    ])
                    ->columnSpanFull(),

            ]);
    }
}
