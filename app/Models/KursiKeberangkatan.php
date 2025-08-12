<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KursiKeberangkatan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'keberangkatan_id',
        'name',
        'row',
        'column',
        'tipe_kelas',
        'is_available',
    ];

    protected $casts = [
        'is_available' => 'boolean',
    ];

    public function keberangkatan() {
        return $this->belongsTo(Keberangkatan::class, 'keberangkatan_id');
    }

    public function passenger(){
        return $this->hasOne(TransaksiPassenger::class);
    }
}
