<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Social extends Model
{
    use HasFactory;

    protected $table = 'socials';
    protected $primaryKey = 'id';
    public $timestamps = true;
    public $incrementing = true;

    public function restaurant() {
        return $this -> belongsTo(Restaurant::class);
    }
}
