<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassKeberangkatan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'keberangkatan_id',
        'tipe_kelas',
        'harga',
        'total_kursi',
    ];

    public function keberangkatan() {
        return $this->belongsTo(Keberangkatan::class, 'keberangkatan_id');
    }

    public function facilities() {
        return $this->belongsToMany(Fasilitas::class, 'keberangkatan_class_fasilitas', 'keberangkatan_class_id', 'fasilitas_id');
    }

    public function transactions(){
        return $this->hasMany(Transaksi::class);
    }
}
