<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    protected $guarded = [];

    public function user(){
        return $this->belongsToMany(User::class , 'kelas_user');
    }
}
