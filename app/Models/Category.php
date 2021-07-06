<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    public function translations() {
        return $this->hasMany('App\Models\CategoriesTranslation');
    }

    public function subcategories() {
        return $this->hasMany('App\Models\Subcategory');
    }

    public function restaurant() {
        return $this->belongsTo('App\Models\Restaurant');
    }
}
