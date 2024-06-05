<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Items extends Model
{
    use HasFactory;

    protected $table = 'items';

    protected $fillable = [
        'name',
        'description',
        'price',
        'vat',
        'img',
        'brand_id',
        'categories_id',
        'amount',
        'reserved',
        'sold',
    ];

    protected $attributes = [
        'amount' => 0,
        'reserved' => 0,
        'sold' => 0,
    ];

    public function brands()
    {
        return $this->belongsTo(Brands::class);
    }

    public function category()
    {
        return $this->belongsTo(Categories::class, 'categories_id');
    }
}
