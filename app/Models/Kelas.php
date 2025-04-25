<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsToMany(User::class, 'kelas_user');
    }

    public function siswa()
    {
        return $this->belongsToMany(User::class, 'kelas_user')
                    ->where('role', 'siswa');
    }

    public function guru()
    {
        return $this->belongsToMany(User::class, 'kelas_user')
                    ->where('role', 'guru');
    }

    public function tugas()
    {
        return $this->belongsToMany(Tugas::class, 'tugas_kelas');
    }
}
