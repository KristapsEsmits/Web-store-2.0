<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $table = 'cart';
    protected $primaryKey = 'cart_id';

    protected $fillable = [
        'user_id',
        'item_id',
        'created_at',
        'updated_at'
    ];

    public function item()
    {
        return $this->belongsTo(Items::class, 'item_id');
    }
}

