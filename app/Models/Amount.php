<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Amount extends Model
{
    use HasFactory;

    protected $guarded = [
    ];

    public $timestamps = false;

    public function translations() {
        return $this->hasMany('App\Models\AmountTranslation');
    }

    public function item() {
        return $this->belongsTo('App\Item');
    }
}
