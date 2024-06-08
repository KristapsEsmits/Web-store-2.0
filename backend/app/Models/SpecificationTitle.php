<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecificationTitle extends Model
{
    use HasFactory;

    protected $table = 'specification_titles';

    protected $fillable = [
        'specification_title',
        'category_id',
    ];

    public function category()
    {
        return $this->belongsTo(Categories::class, 'category_id');
    }

    public function specificationDescriptions()
    {
        return $this->hasMany(SpecificationDescription::class, 'specification_title_id');
    }
}
