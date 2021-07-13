<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubcategoriesTranslation extends Model
{
    use HasFactory;

    protected $guarded = [
    ];

    public $timestamps = false;

    public function subcategory() {
        return $this->belongsTo('App\Subcategory');
    }
}
