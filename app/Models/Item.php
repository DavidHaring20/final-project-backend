<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    public function translations() {
        return $this->hasMany('App\Models\ItemTranslation');
    }

    public function amounts() {
        return $this->hasMany('App\Models\Amount');
    }

    public function subcategory() {
        return $this->belongsTo('App\Subcategory');
    }
}
