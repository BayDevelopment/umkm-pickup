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
                    ->getStateUsing(
                        fn($record) => filled($record?->image)
                            ? asset('storage/' . (is_array($record->image)
                                ? $record->image[0]
                                : $record->image))
                            : asset('images/no-image.png')
                    )
                    ->width(50)
                    ->circular(),

                // 🔹 NAME
                TextColumn::make('name')
                    ->searchable(['name', 'slug'])
                    ->sortable()
                    ->weight('bold')
                    ->limit(30),

                // 🔹 TYPE (BADGE 🔥)
                TextColumn::make('type')
                    ->badge()
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
                    ->toggleable(),

                // 🔹 VARIANT COUNT (kalau pakai variant)
                TextColumn::make('variants_count')
                    ->counts('variants')
                    ->label('Varian')
                    ->sortable(),

                // 🔹 STATUS
                IconColumn::make('is_active')
                    ->boolean()
                    ->label('Status')
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),

                // 🔹 CREATED AT
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(),

                // 🔹 UMKM (HANYA ADMIN 🔐)
                TextColumn::make('umkm.name')
                    ->label('UMKM')
                    ->visible(fn() => Auth::user()->role === 'admin'),
            ])

            ->filters([

                // 🔹 FILTER TYPE
                SelectFilter::make('type')
                    ->options([
                        'food' => 'Makanan',
                        'drink' => 'Minuman',
                        'fashion' => 'Fashion',
                    ]),

                // 🔹 FILTER CATEGORY
                SelectFilter::make('category_id')
                    ->relationship('category', 'name')
                    ->label('Kategori'),

                // 🔹 FILTER STATUS
                TernaryFilter::make('is_active')
                    ->label('Status'),

                // 🔹 TRASH
                TrashedFilter::make(),
            ])

            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->label('Lihat')
                        ->icon('heroicon-o-eye')
                        ->color('gray'),

                    EditAction::make()
                        ->label('Edit')
                        ->icon('heroicon-o-pencil-square')
                        ->color('primary'),

                    DeleteAction::make()
                        ->label('Hapus')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Hapus data?')
                        ->modalDescription('Data yang dihapus tidak bisa dikembalikan.')
                        ->successNotification(
                            Notification::make()
                                ->title('Terhapus')
                                ->body('Data berhasil dihapus.')
                                ->success()
                        ),
                ])
                    ->label('Aksi')
                    ->icon('heroicon-o-ellipsis-vertical')
                    ->button()
                    ->outlined()
                    ->tooltip('Aksi data')
                    ->dropdownPlacement('bottom-end'),
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
