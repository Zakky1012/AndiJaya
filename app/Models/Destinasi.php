<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Destinasi extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'iata_code',
        'rute_perjalanan',
        'gambar',
        'kota',
    ];

    public function segments() {
        return $this->hasMany(SegmentKeberangkatan::class);
    }
    
}
