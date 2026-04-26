<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                // 🔹 IMAGE
                ImageColumn::make('image')
                    ->label('Image')
                    ->disk('public')
                    ->defaultImageUrl(asset('images/no-image.png'))
                    ->circular()
                    ->size(50),

                // 🔹 NAME + SLUG
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(30)
                    ->description(fn($record) => $record->slug),

                // 🔹 TYPE
                TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn($state) => ucfirst($state))
                    ->color(fn($state) => match ($state) {
                        'food' => 'success',
                        'drink' => 'info',
                        'fashion' => 'warning',
                        default => 'gray',
                    }),

                // 🔹 CATEGORY
                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->sortable()
                    ->searchable()
                    ->placeholder('-'),

                // 🔹 PRICE (kalau ada di DB)
                TextColumn::make('price')
                    ->money('IDR')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // 🔹 VARIANT COUNT
                TextColumn::make('variants_count')
                    ->counts('variants')
                    ->label('Varian')
                    ->sortable(),

                // 🔹 STATUS
                IconColumn::make('is_active')
                    ->boolean()
                    ->label('Status')
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                // 🔹 CREATED
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(),

                // 🔹 UMKM (ROLE BASED)
                TextColumn::make('umkm.name')
                    ->label('UMKM')
                    ->visible(fn() => Auth::user()?->role === 'admin')
                    ->placeholder('-'),
            ])

            ->filters([

                // 🔹 TYPE
                SelectFilter::make('type')
                    ->options([
                        'food' => 'Makanan',
                        'drink' => 'Minuman',
                        'fashion' => 'Fashion',
                    ])
                    ->label('Tipe'),

                // 🔹 CATEGORY
                SelectFilter::make('category_id')
                    ->relationship('category', 'name')
                    ->label('Kategori')
                    ->searchable(),

                // 🔹 STATUS
                TernaryFilter::make('is_active')
                    ->label('Status'),

                // 🔹 TRASH
                TrashedFilter::make(),
            ])

            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),

                    EditAction::make(),

                    DeleteAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Hapus data?')
                        ->modalDescription('Data tidak bisa dikembalikan!')
                        ->successNotification(
                            Notification::make()
                                ->title('Berhasil')
                                ->body('Data berhasil dihapus')
                                ->success()
                        ),
                ])
                    ->label('Aksi')
                    ->icon('heroicon-o-ellipsis-vertical')
                    ->button()
                    ->outlined(),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
