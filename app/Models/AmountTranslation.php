<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AmountTranslation extends Model
{
    use HasFactory;

    public function amount() {
        return $this->belongsTo('App\Amount');
    }
}
