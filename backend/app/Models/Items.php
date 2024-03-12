<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Brands;


class Items extends Model
{
    use HasFactory;

    protected $table = 'items';

    protected $fillable = [
        'name',
        'description',
        'price',
        'img',
        'brand_id',
        'categories_id',
    ];

    public function brands()
    {
        return $this->belongsTo(Brands::class);
    }
}
