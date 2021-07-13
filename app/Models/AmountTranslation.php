<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AmountTranslation extends Model
{
    use HasFactory;

    protected $guarded = [
    ];

    public $timestamps = false;

    public function amount() {
        return $this->belongsTo('App\Amount');
    }
}
