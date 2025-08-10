<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fasilitas extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'gambar',
        'nama',
        'deskripsi',
    ];

    public function classes() {
        return $this->belongsToMany(ClassKeberangkatan::class, 'keberangkatan_class_fasilitas', 'fasilitas_id', 'keberangkatan_class_id');
    }
}
