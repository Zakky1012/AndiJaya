<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaksi extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'kode',
        'keberangkatan_id',
        'keberangkatan_class_id',
        'nama',
        'email',
        'nomor',
        'nomor_passenger',
        'kode_promo_id',
        'status_payment',
        'sub_total',
        'grand_total',
    ];

    public function keberangkatan() {
        return $this->belongsTo(Keberangkatan::class, 'keberangkatan_id');
    }

    public function class() {
        return $this->belongsTo(ClassKeberangkatan::class, 'keberangkatan_class_id');
    }

    public function promo() {
        return $this->belongsTo(PromoCode::class, 'kode_promo_id');
    }

    public function passengers() {
        return $this->hasMany(TransaksiPassenger::class);
    }

}
