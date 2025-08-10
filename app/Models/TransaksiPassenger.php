<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransaksiPassenger extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'transaksi_id',
        'kursi_keberangkatan_id',
        'nama',
        'date_of_birth',
        'kewarganegaraan',
    ];

    public function transaction() {
        return $this->belongsTo(Transaksi::class, 'transaksi_id');
    }

    public function seat() {
        return $this->belongsTo(KursiKeberangkatan::class, 'kursi_keberangkatan_id');
    }
}
