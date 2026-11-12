<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'weight',
        'price_per_kg',
        'total_amount',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'weight' => 'float',
        'price_per_kg' => 'float',
        'total_amount' => 'float',
    ];

    // Relasi ke User (Nasabah)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Kategori Sampah
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function details()
{
    return $this->hasMany(TransactionDetail::class);
}
}