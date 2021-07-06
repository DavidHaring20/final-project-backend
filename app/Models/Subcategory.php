<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    use HasFactory;

    public function translations() {
        return $this->hasMany('App\Models\SubcategoriesTranslation');
    }

    public function items() {
        return $this->hasMany('App\Models\Item');
    }

    public function category() {
        return $this->belongsTo('App\Category');
    }
}
