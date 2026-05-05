<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kecamatan extends Model
{
    protected $table = 'kecamatan';
    public $timestamps = false;

    protected $fillable = ['nama_kecamatan'];

    public function tempats()
    {
        return $this->hasMany(Tempat::class, 'kecamatan_id');
    }
}