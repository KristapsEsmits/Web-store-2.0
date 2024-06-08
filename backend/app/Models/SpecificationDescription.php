<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecificationDescription extends Model
{
    use HasFactory;

    protected $table = 'specification_description';

    protected $fillable = [
        'description',
        'item_id',
        'specification_title_id',
    ];

    public function item()
    {
        return $this->belongsTo(Items::class, 'item_id');
    }

    public function specificationTitle()
    {
        return $this->belongsTo(SpecificationTitle::class, 'specification_title_id');
    }
}
