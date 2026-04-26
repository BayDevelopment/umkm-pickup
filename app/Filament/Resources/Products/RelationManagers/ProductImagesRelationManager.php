<?php

namespace App\Filament\Resources\Products\RelationManagers;

use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductImagesRelationManager extends RelationManager
{
    protected static string $relationship = 'images';
    protected static ?string $title = 'Gambar Produk';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('path')
                    ->label('Gambar')
                    ->image()
                    ->disk('public')
                    ->directory('products')
                    ->required()
                    ->maxSize(1024)
                    ->acceptedFileTypes(['image/jpeg', 'image/png'])
                    ->helperText('Format: JPG, PNG • Maksimal 1MB')
                    ->columnSpanFull(),

                Grid::make(2)->schema([

                    Toggle::make('is_main')
                        ->label('Jadikan Gambar Utama')
                        ->default(false),

                    TextInput::make('sort_order')
                        ->label('Urutan')
                        ->numeric()
                        ->default(0)
                        ->minValue(0)
                        ->rules([
                            'nullable',
                            'integer',
                            'min:0',
                        ]),

                ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('path')
            ->columns([
                ImageColumn::make('path')
                    ->label('Gambar')
                    ->getStateUsing(fn($record) => asset('storage/' . $record->path))
                    ->circular()
                    ->width(80),

                IconColumn::make('is_main')
                    ->label('Utama')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-star')
                    ->trueColor('warning')
                    ->falseColor('gray'),

                TextColumn::make('sort_order')
                    ->label('Urutan')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Ditambahkan')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->defaultSort('sort_order', 'asc')
            ->reorderable('sort_order') // ← drag & drop urutan gambar
            ->headerActions([
                CreateAction::make()
                    ->label('Tambah Gambar')
                    ->icon('heroicon-o-photo')
                    ->modalSubmitAction(
                        fn($action) => $action
                            ->label('Simpan Gambar')
                            ->icon('heroicon-o-check-circle')
                            ->color('success')
                    )
                    ->modalCancelAction(
                        fn($action) => $action
                            ->label('Batal')
                            ->icon('heroicon-o-x-mark')
                            ->color('gray')
                    )
                    ->successNotification(
                        Notification::make()
                            ->title('Berhasil')
                            ->body('Gambar berhasil ditambahkan.')
                            ->success()
                    ),
            ])
            ->recordActions([
                ActionGroup::make([
                    DeleteAction::make()
                        ->label('Hapus')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Gambar?')
                        ->modalDescription('Gambar akan dihapus permanen.')
                        ->after(function ($record) {
                            // Hapus file dari storage setelah record dihapus
                            \Illuminate\Support\Facades\Storage::disk('public')
                                ->delete($record->path);
                        })
                        ->successNotification(
                            Notification::make()
                                ->title('Berhasil')
                                ->body('Gambar berhasil dihapus.')
                                ->success()
                        ),
                ])
                    ->label('Aksi')
                    ->icon('heroicon-o-ellipsis-vertical')
                    ->button()
                    ->outlined()
                    ->tooltip('Aksi data')
                    ->dropdownPlacement('bottom-end')
            ])
            ->bulkActions([
                DeleteBulkAction::make()
                    ->label('Hapus Terpilih')
                    ->requiresConfirmation(),
            ]);
    }
}
