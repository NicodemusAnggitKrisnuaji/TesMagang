<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    protected $table = 'pegawais';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = ['image', 'name', 'phone', 'id_divisi', 'position'];

    public function division()
    {
        return $this->belongsTo(Division::class, 'id_divisi', 'id');
    }
}
