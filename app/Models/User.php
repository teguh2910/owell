<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail; // Ini bisa dihapus jika tidak pakai verifikasi email
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable // Hapus 'implements MustVerifyEmail' jika tidak pakai verifikasi email
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // Tambahkan ini
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime', // Ini bisa dihapus jika tidak pakai verifikasi email
            'password' => 'hashed',
        ];
    }
}