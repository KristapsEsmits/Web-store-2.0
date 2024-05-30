<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FavoriteItems extends Model
{
    use HasFactory;

    protected $table = 'favorite_items';
    protected $primaryKey = 'favorite_id';

    protected $fillable = [
        'user_id',
        'item_id',
        'created_at',
        'updated_at'
    ];

    public function item()
    {
        return $this->belongsTo(Items::class);
    }
}
