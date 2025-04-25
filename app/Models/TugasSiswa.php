<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TugasSiswa extends Model
{
    protected $guarded = [];

    
    public function tugas()
    {
        return $this->belongsTo(Tugas::class);
    }



    public function siswa()
    {
        return $this->belongsTo(User::class);
    }

}
