<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemTranslation extends Model
{
    use HasFactory;

    protected $guarded = [
    ];

    public $timestamps = false;

    public function item() {
        return $this->belongsTo('App\Item');
    }
}
