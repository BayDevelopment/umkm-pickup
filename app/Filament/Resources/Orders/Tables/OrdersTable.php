<?php

namespace App\Filament\Resources\Orders\Tables;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Infolists\Components\ImageEntry;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Svg\Tag\Circle;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('Order ID')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('total_price')
                    ->label('Total')
                    ->money('IDR', locale: 'id')
                    ->sortable(),

                BadgeColumn::make('payment_status')
                    ->label('Payment')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'paid',
                        'danger' => 'rejected',
                    ])
                    ->sortable(),

                BadgeColumn::make('status')
                    ->label('Order Status')
                    ->colors([
                        'warning' => 'pending',
                        'primary' => 'processing',
                        'success' => 'completed',
                        'danger' => 'cancelled',
                    ])
                    ->sortable(),

                ImageColumn::make('payment_proof')
                    ->getStateUsing(fn($record) => asset('storage/' . $record->payment_proof))
                    ->circular()
                    ->width(50),

                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                TrashedFilter::make(),

                SelectFilter::make('payment_status')
                    ->label('Payment Status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'rejected' => 'Rejected',
                    ])
                    ->placeholder('Semua Status'),
            ])
            ->recordActions([
                ActionGroup::make([

                    EditAction::make()
                        ->label('Edit')
                        ->icon('heroicon-o-pencil-square')
                        ->color('primary')
                        ->visible(fn($record) => ! $record->trashed()),

                    DeleteAction::make()
                        ->label('Hapus')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Hapus data?')
                        ->modalDescription('Data akan dipindahkan ke trash.')
                        ->successNotificationTitle('Data berhasil dipindahkan ke trash.')
                        ->visible(fn($record) => ! $record->trashed()),

                    RestoreAction::make()
                        ->label('Restore')
                        ->icon('heroicon-o-arrow-path')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Restore data?')
                        ->modalDescription('Data akan dikembalikan.')
                        ->successNotificationTitle('Data berhasil direstore.')
                        ->visible(fn($record) => $record->trashed()),

                    ForceDeleteAction::make()
                        ->label('Hapus Permanen')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Hapus permanen?')
                        ->modalDescription('Data akan dihapus permanen dan tidak bisa dikembalikan.')
                        ->successNotificationTitle('Data berhasil dihapus permanen.')
                        ->visible(fn($record) => $record->trashed()),

                    Action::make('view_payment_proof')
                        ->label('Lihat Bukti')
                        ->icon('heroicon-o-eye')
                        ->modalHeading('Bukti Transfer')
                        ->modalSubmitAction(false) // ⛔ hapus tombol Kirim
                        ->modalCancelActionLabel('Tutup') // optional ganti label Batal
                        ->modalContent(
                            fn($record) => $record->payment_proof
                                ? new HtmlString('<img src="' . asset('storage/' . $record->payment_proof) . '" class="w-full h-auto rounded shadow-lg">')
                                : new HtmlString('<p class="text-gray-500">Tidak ada bukti transfer</p>')
                        ),

                ])
                    ->label('Aksi')
                    ->icon('heroicon-o-ellipsis-vertical')
                    ->button()
                    ->outlined()
                    ->tooltip('Aksi data')
                    ->dropdownPlacement('bottom-end')
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
