<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SegmentKeberangkatan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sequence',
        'keberangkatan_id',
        'destinasi_id',
        'time',
    ];

    protected $casts = [
        'time' => 'datetime'
    ];

    public function keberangkatan(){
        return $this->belongsTo(Keberangkatan::class, 'keberangkatan_id');
    }

    public function destinasi(){
        return $this->belongsTo(Destinasi::class, 'destinasi_id');
    }
}
