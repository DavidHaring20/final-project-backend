<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    use HasFactory;

    protected $guarded = [
    ];

    public $timestamps = false;

    public function languages() {
        return $this->belongsToMany('App\Models\Language');
    }

    public function styles() {
        return $this->hasMany('App\Models\Style');
    }

    public function networks() {
        return $this->hasMany('App\Models\SocialNetwork');
    }

    public function translations() {
        return $this->hasMany('App\Models\RestaurantTranslation');
    }

    public function categories() {
        return $this->hasMany('App\Models\Category');
    }
}
