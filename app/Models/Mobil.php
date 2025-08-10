<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mobil extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nomor_plat',
        'nama_mobil',
        'jenis_mobil',
    ];

    public function keberangkatans(){
        return $this->hasMany(Keberangkatan::class);
    }
}
