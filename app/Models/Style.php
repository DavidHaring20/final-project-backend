<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Style extends Model
{
    use HasFactory;

    protected $guarded = [
    ];

    protected $table = 'styles';
    protected $primaryKey = 'restaurant_id';
    public $timestamps = false;

    public function restaurant() {
        return $this->belongsTo('App\Restaurant');
    }
}
