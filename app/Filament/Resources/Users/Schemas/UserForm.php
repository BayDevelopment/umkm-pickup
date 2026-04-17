<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi User')
                ->columns(1)
                ->schema([

                    TextInput::make('name')
                        ->required()
                        ->minLength(3)
                        ->maxLength(100)
                        ->regex('/^[a-zA-Z0-9\s]+$/')
                        ->placeholder('Masukan nama/username')
                        ->validationMessages([
                            'required' => 'Nama wajib diisi',
                            'minLength' => 'Minimal 3 karakter',
                            'regex' => 'Nama hanya boleh huruf dan angka',
                        ]),

                    TextInput::make('email')
                        ->email()
                        ->required()
                        ->placeholder('Masukan email')
                        ->unique(ignoreRecord: true)
                        ->maxLength(150)
                        ->validationMessages([
                            'required' => 'Email wajib diisi',
                            'email' => 'Format email tidak valid',
                            'unique' => 'Email sudah digunakan',
                        ]),

                    Select::make('role')
                        ->required()
                        ->options([
                            'admin' => 'Admin',
                            'customer' => 'Customer',
                            'owner' => 'Owner',
                        ])
                        ->default('customer')
                        ->native(false)
                        ->validationMessages([
                            'required' => 'Role wajib dipilih',
                        ]),

                    Select::make('status')
                        ->label('Status Akun')
                        ->required()
                        ->options([
                            'pending' => 'Pending',
                            'active' => 'Active',
                            'suspended' => 'Suspended',
                        ])
                        ->default(fn() => Auth::user()->role === 'admin' ? 'active' : 'pending')
                        ->disabled(fn() => Auth::user()->role !== 'admin')
                        ->native(false)
                        ->validationMessages([
                            'required' => 'Status wajib dipilih',
                        ]),
                ]),

            Section::make('Keamanan')
                ->schema([

                    Grid::make(1)->schema([

                        TextInput::make('password')
                            ->password()
                            ->minLength(8)
                            ->maxLength(100)
                            ->regex('/^(?=.*[A-Za-z])(?=.*\d).+$/') // wajib huruf + angka
                            ->placeholder('Masukan password')
                            ->autocomplete('new-password')
                            ->required(fn(string $operation) => $operation === 'create')
                            ->dehydrateStateUsing(fn($state) => filled($state) ? Hash::make($state) : null)
                            ->dehydrated(fn($state) => filled($state))
                            ->helperText('Minimal 8 karakter, kombinasi huruf & angka.')
                            ->validationMessages([
                                'required' => 'Password wajib diisi',
                                'minLength' => 'Minimal 8 karakter',
                                'regex' => 'Password harus mengandung huruf dan angka',
                            ]),

                    ]),
                ]),
        ]);
    }
}
