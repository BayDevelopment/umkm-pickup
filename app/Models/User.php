<?php

namespace App\Models;

use Illuminate\Auth\Notifications\ResetPassword;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Validation\ValidationException;

class User extends Authenticatable implements MustVerifyEmail, FilamentUser
{
    use HasFactory, Notifiable;
    protected $fillable = [
        'name',
        'email',
        'password',
        'role'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function sendPasswordResetNotification($token): void
    {
        if ($this->role !== 'admin') {
            throw ValidationException::withMessages([
                'email' => 'Email ini bukan akun admin.',
            ]);
        }

        $this->notify(new ResetPassword($token));
    }

    // Relstionship
    public function umkm()
    {
        return $this->hasOne(umkmModel::class, 'user_id');
    }


    public function canAccessPanel(Panel $panel): bool
    {
        return in_array($this->role, ['admin', 'owner']);
        // Hapus && $this->status === 'active'
        // Biarkan middleware yang handle redirect pending
    }
}
