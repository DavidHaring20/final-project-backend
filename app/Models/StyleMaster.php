<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StyleMaster extends Model
{
    use HasFactory;
    
    protected $primaryKey = 'id';
    protected $table = 'style_default_property_values';
    public $timestamps = false;
    public $incrementing = true;

    protected $guarded = [];
}
