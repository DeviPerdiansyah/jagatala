<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
   protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'balance',
        'phone',
        'address',
        'rt_rw',
        'city',
        'status',
        'birth_date',
        'account_number',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

// Relasi ke Transaksi Setor
public function transactions()
{
    return $this->hasMany(Transaction::class);
}

// Relasi ke Penarikan Saldo
public function withdrawals()
{
    return $this->hasMany(Withdrawal::class);
}

// Helper method cepat
public function isPengelola(): bool
{
    return $this->role === 'pengelola';
}

public function isActive(): bool
{
    return $this->status === 'active';
}

}