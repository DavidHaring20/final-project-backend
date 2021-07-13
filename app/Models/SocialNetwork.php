<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialNetwork extends Model
{
    use HasFactory;

    protected $guarded = [
    ];

    public $timestamps = false;

    public function restaurant() {
        return $this->belongsTo('App\Restaurant');
    }
}
