<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    use HasFactory;

    protected $table = 'admins';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;

    public function user() {
        return $this->belongsTo(User::class);
    }
}
