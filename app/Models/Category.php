<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'unit', // Tambahkan 'unit'
        'price_per_kg',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}