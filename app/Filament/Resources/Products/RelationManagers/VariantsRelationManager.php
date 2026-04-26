<?php

namespace App\Filament\Resources\Products\RelationManagers;

use App\Filament\Resources\Products\ProductResource;
use App\Models\ProductVariantModel;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class VariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Variant')
                    ->columnSpanFull()
                    ->schema([

                        Grid::make(2)->schema([

                            TextInput::make('sku')
                                ->label('SKU')
                                ->placeholder('Contoh: TS-MERAH-M')
                                ->nullable()
                                ->columnSpanFull()
                                ->unique(ignoreRecord: true)
                                ->maxLength(100)
                                ->rules([
                                    'nullable',
                                    'string',
                                    'max:100',
                                    'alpha_dash',         // hanya huruf, angka, - dan _
                                    'unique:product_variants,sku', // pastikan unik di DB
                                ]),

                            KeyValue::make('attributes')
                                ->label('Atribut Variant')
                                ->keyLabel('Nama')
                                ->valueLabel('Nilai')
                                ->addButtonLabel('Tambah Atribut')
                                ->nullable()
                                ->columnSpanFull()
                                ->rules([
                                    'nullable',
                                    'array',        // harus berupa array key-value
                                    'max:10',       // maksimal 10 atribut
                                ]),

                        ]),

                        Grid::make(2)->schema([

                            TextInput::make('price')
                                ->label('Harga')
                                ->numeric()
                                ->required()
                                ->prefix('Rp')
                                ->minValue(0)
                                ->maxValue(99999999) // maksimal 99 juta
                                ->rules([
                                    'required',
                                    'numeric',
                                    'min:0',
                                    'max:99999999',
                                ]),

                            Select::make('branch_id')
                                ->label('Cabang')
                                ->relationship('branch', 'name')
                                ->searchable()
                                ->preload()
                                ->nullable()
                                ->placeholder('Semua Cabang / Tidak ditentukan')
                                ->noSearchResultsMessage('Cabang tidak ditemukan.')
                                ->searchPrompt('Ketik untuk mencari cabang...')
                                ->loadingMessage('Memuat cabang...')
                                ->rules([
                                    'nullable',
                                    'exists:branches,id',
                                ]),

                        ]),

                        TextInput::make('stock')
                            ->label('Stok')
                            ->numeric()
                            ->required()
                            ->default(0)
                            ->minValue(0)
                            ->maxValue(99999) // maksimal 99999 stok
                            ->columnSpanFull()
                            ->rules([
                                'required',
                                'integer',
                                'min:0',
                                'max:99999',
                            ]),

                    ])
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('sku')
            ->columns([
                TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable(),

                TextColumn::make('attributes')
                    ->label('Atribut')
                    ->formatStateUsing(function ($state) {
                        if (! $state) return '-';

                        return collect($state)
                            ->map(fn($v, $k) => ucfirst($k) . ': ' . $v)
                            ->implode("\n");
                    })
                    ->wrap(),

                TextColumn::make('price')
                    ->label('Harga')
                    ->money('IDR', locale: 'id'),

                TextColumn::make('branch.name')
                    ->label('Cabang')
                    ->badge()
                    ->color('info'),

                TextColumn::make('stock')
                    ->label('Stok')
                    ->badge()
                    ->color(
                        fn($state) =>
                        $state <= 0 ? 'danger' : ($state <= 5 ? 'warning' : 'success')
                    ),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                TrashedFilter::make(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Tambah Variant')
                    ->icon('heroicon-o-plus')

                    // Tombol Simpan jadi hijau
                    ->modalSubmitAction(
                        fn($action) => $action
                            ->label('Simpan Variant')
                            ->icon('heroicon-o-check-circle')
                            ->color('success')
                    )

                    // Notifikasi sukses custom (AMAN SEMUA VERSI)
                    ->successNotification(
                        Notification::make()
                            ->title('Berhasil')
                            ->body('Data variant berhasil ditambahkan.') // ← lebih spesifik
                            ->success()
                    )

                    // Aktifkan create another
                    ->createAnother()

                    // Tombol Cancel
                    ->modalCancelAction(
                        fn($action) => $action
                            ->label('Batal')
                            ->icon('heroicon-o-x-mark')
                            ->color('gray')
                    ),
            ])

            ->recordActions([
                ActionGroup::make([
                    Action::make('manageStock')
                        ->label('Kelola Stok')
                        ->icon('heroicon-o-cube')
                        ->color('success')
                        ->url(fn($record) => ProductResource::getUrl('edit', [
                            'record' => $record->product_id,
                        ])),
                    EditAction::make()
                        ->label('Edit')
                        ->icon('heroicon-o-pencil-square')
                        ->color('primary')
                        ->visible(fn($record) => ! $record->trashed())
                        ->modalSubmitAction(
                            fn($action) => $action
                                ->label('Simpan Perubahan')
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
                                ->body('Data variant berhasil diupdate.')
                                ->success()
                        ),

                    DeleteAction::make()
                        ->label('Hapus')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Variant?')
                        ->modalDescription('Data akan dipindahkan ke trash.')
                        ->successNotificationTitle('Data berhasil dipindahkan ke trash.')
                        ->visible(fn($record) => ! $record->trashed()),

                    RestoreAction::make()
                        ->label('Restore')
                        ->icon('heroicon-o-arrow-path')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Restore Variant?')
                        ->modalDescription('Data akan dikembalikan.')
                        ->successNotificationTitle('Data berhasil direstore.')
                        ->visible(fn($record) => $record->trashed()),

                    ForceDeleteAction::make()
                        ->label('Hapus Permanen')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Permanen?')
                        ->modalDescription('Data akan dihapus permanen dan tidak bisa dikembalikan.')
                        ->successNotificationTitle('Data berhasil dihapus permanen.')
                        ->visible(fn($record) => $record->trashed()),
                ])
                    ->label('Aksi')
                    ->icon('heroicon-o-ellipsis-vertical')
                    ->button()
                    ->outlined()
                    ->tooltip('Aksi data')
                    ->dropdownPlacement('bottom-end')
            ]);
    }
}
