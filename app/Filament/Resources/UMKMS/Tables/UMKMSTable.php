<?php

namespace App\Filament\Resources\UMKMS\Tables;

use App\Models\umkmModel;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class UMKMSTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                // 🔹 NAMA UMKM
                TextColumn::make('name')
                    ->label('Nama UMKM')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                // 🔹 KOTA
                TextColumn::make('city')
                    ->label('Kota')
                    ->sortable()
                    ->toggleable(),

                // 🔹 NIK (SENSOR ⚠️)
                TextColumn::make('ktp_number')
                    ->label('NIK')
                    ->formatStateUsing(fn($state) => substr($state, 0, 4) . '********')
                    ->tooltip(fn($record) => $record->ktp_number),

                // 🔥 STATUS VERIFIKASI (PALING PENTING)
                TextColumn::make('verification_status')
                    ->label('Status')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                    }),

                // 🔹 CATATAN ADMIN
                TextColumn::make('verification_note')
                    ->label('Catatan')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),

                // 🔹 TANGGAL DAFTAR
                TextColumn::make('created_at')
                    ->label('Tanggal Daftar')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->filters([

                // 🔥 FILTER STATUS
                SelectFilter::make('verification_status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),

                // 🔹 FILTER KOTA
                SelectFilter::make('city')
                    ->options(
                        fn() => umkmModel::query()
                            ->pluck('city', 'city')
                            ->filter()
                            ->unique()
                            ->toArray()
                    ),

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
                ]),
            ]);
    }
}
