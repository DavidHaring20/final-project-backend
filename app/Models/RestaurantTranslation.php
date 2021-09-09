<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestaurantTranslation extends Model
{
    use HasFactory;

    protected $guarded = [
    ];

    public $timestamps = false;
    public $incrementing = true;
    protected $table = 'restaurant_translations';
    protected $primaryKey = 'id';

    public function restaurant() {
        return $this->belongsTo('App\Restaurant');
    }
}
