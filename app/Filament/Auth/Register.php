<?php

namespace App\Filament\Auth;

use Filament\Auth\Pages\Register as BaseRegister;
use App\Models\User;
use App\Notifications\RegistrationPendingNotification;
use Illuminate\Support\Facades\Hash;

class Register extends BaseRegister
{

    protected function handleRegistration(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),

            'role' => 'owner',
            'status' => 'pending',
        ]);

        // Kirim email ke owner
        $user->notify(new RegistrationPendingNotification());

        return $user;
    }
}
