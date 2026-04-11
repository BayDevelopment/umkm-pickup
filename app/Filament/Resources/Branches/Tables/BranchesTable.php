<?php

namespace App\Filament\Resources\Branches\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class BranchesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Foto')
                    ->getStateUsing(
                        fn($record) => filled($record?->image)
                            ? asset('storage/' . $record->image)
                            : asset('images/no-image.png')
                    )
                    ->square()
                    ->size(50),

                TextColumn::make('name')
                    ->label('Nama Cabang')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('city')
                    ->label('Kota')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('phone')
                    ->label('Telepon')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Nomor telepon disalin')
                    ->toggleable(),

                IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),

                TextColumn::make('latitude')
                    ->label('Latitude')
                    ->formatStateUsing(
                        fn($state) =>
                        $state ? number_format((float) $state, 6) : '-'
                    )
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('longitude')
                    ->label('Longitude')
                    ->formatStateUsing(
                        fn($state) =>
                        $state ? number_format((float) $state, 6) : '-'
                    )
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('opening_time')
                    ->label('Jam Buka')
                    ->time('H:i')
                    ->toggleable(),

                TextColumn::make('closing_time')
                    ->label('Jam Tutup')
                    ->time('H:i')
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Status Cabang')
                    ->trueLabel('Aktif')
                    ->falseLabel('Nonaktif')
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()
                        ->label('Edit')
                        ->icon('heroicon-o-pencil-square')
                        ->color('primary'),

                    DeleteAction::make()
                        ->label('Hapus')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Cabang?')
                        ->modalDescription('Data cabang akan dihapus permanen dan tidak dapat dikembalikan.')
                        ->modalSubmitActionLabel('Ya, Hapus')
                        ->successNotification(
                            Notification::make()
                                ->title('Berhasil')
                                ->body('Data cabang berhasil dihapus.')
                                ->success()
                        ),

                ])
                    ->label('Aksi')
                    ->icon('heroicon-o-ellipsis-vertical')
                    ->button()
                    ->outlined()
                    ->tooltip('Aksi')
                    ->dropdownPlacement('bottom-end')
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
